<?php

namespace App\Services;

use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/** Store collection follows the showroom's opening hours, not install capacity. */
class PickupScheduleService
{
    public const DAYS_AHEAD = 30;

    public function __construct(private readonly BookingService $bookings)
    {
    }

    public function availableDates(): Collection
    {
        return collect(range(0, self::DAYS_AHEAD))
            ->map(fn (int $offset) => now()->startOfDay()->addDays($offset))
            ->reject(fn (Carbon $date) => $this->bookings->isClosedDate($date))
            ->values();
    }

    public function slotsFor(string $date): Collection
    {
        try { $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay(); } catch (\Throwable) { return collect(); }
        if (! $day->betweenIncluded(now()->startOfDay(), now()->startOfDay()->addDays(self::DAYS_AHEAD)) || $this->bookings->isClosedDate($day)) return collect();

        $start = Carbon::createFromFormat('Y-m-d H:i', $day->format('Y-m-d').' '.setting('BUSINESS_HOURS_START', '09:00'));
        $end = Carbon::createFromFormat('Y-m-d H:i', $day->format('Y-m-d').' '.setting('BUSINESS_HOURS_END', '18:00'));
        $minutes = max(15, (int) setting('BOOKING_SLOT_MINUTES', 30));
        $slots = collect();
        while ($start->copy()->addMinutes($minutes) <= $end) { if ($start->isFuture()) $slots->push($start->format('H:i')); $start->addMinutes($minutes); }
        return $slots;
    }

    public function isValid(string $date, string $time): bool { return $this->slotsFor($date)->contains($time); }
    public function pickupAt(string $date, string $time): Carbon { return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}"); }
}
