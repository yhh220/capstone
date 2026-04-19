<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    public function getAvailableSlots(Service $service, Carbon $date): Collection
    {
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . setting('BUSINESS_HOURS_START', '09:00'));
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . setting('BUSINESS_HOURS_END', '18:00'));
        $slots = collect();

        while ($start->copy()->addMinutes($service->duration_minutes) <= $end) {
            if ($this->isSlotAvailable($service, $start)) {
                $slots->push($start->format('H:i'));
            }

            $start->addMinutes(30);
        }

        return $slots;
    }

    public function isSlotAvailable(Service $service, Carbon $startAt, ?int $ignoreBookingId = null): bool
    {
        $endAt = $startAt->copy()->addMinutes($service->duration_minutes + $service->buffer_after);

        return !Booking::query()
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where('service_id', $service->id)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->where(function ($query) use ($startAt, $endAt) {
                $query
                    ->whereBetween('start_at', [$startAt, $endAt])
                    ->orWhereBetween('end_at', [$startAt, $endAt])
                    ->orWhere(function ($nested) use ($startAt, $endAt) {
                        $nested->where('start_at', '<=', $startAt)
                            ->where('end_at', '>=', $endAt);
                    });
            })
            ->exists();
    }

    public function buildStartAt(string $date, string $time): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}");
    }

    public function buildEndAt(Service $service, Carbon $startAt): Carbon
    {
        return $startAt->copy()->addMinutes($service->duration_minutes + $service->buffer_after);
    }
}
