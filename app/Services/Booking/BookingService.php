<?php

namespace App\Services\Booking;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    /**
     * Length of one showroom visit slot, in minutes. Set by the admin
     * (Settings → Appointment Slot Length) — not hardcoded. A booking is just
     * a time to meet at the store, so every slot is the same length regardless
     * of what the customer wants to discuss.
     */
    public function visitMinutes(): int
    {
        return max(15, (int) setting('BOOKING_SLOT_MINUTES', 30));
    }

    public function getAvailableSlots(Carbon $date): Collection
    {
        if ($this->isClosedDate($date)) {
            return collect();
        }

        $start = Carbon::parse($date->format('Y-m-d').' '.setting('BUSINESS_HOURS_START', '09:00'));
        $end = Carbon::parse($date->format('Y-m-d').' '.setting('BUSINESS_HOURS_END', '18:00'));
        $length = $this->visitMinutes();

        // Fetch the day's bookings once and check each candidate slot in memory,
        // instead of one query per slot (isSlotAvailable() below) — that was
        // ~18 sequential round-trips against the remote DB on every render of
        // this step, which is fine on local SQLite but visibly slow in
        // production (e.g. clicking "Back" into this step).
        $dayBookings = Booking::query()
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->get(['start_at', 'end_at']);

        $slots = collect();
        $now = now();

        while ($start->copy()->addMinutes($length) <= $end) {
            $slotEnd = $start->copy()->addMinutes($length);

            // A slot must still be in the future — picking "today" used to list
            // the whole day including hours already gone, and the customer only
            // found out at the final submit step ("that time has already passed").
            $isAvailable = $start->gt($now) && ! $dayBookings->contains(
                fn (Booking $booking) => $booking->start_at->lt($slotEnd) && $booking->end_at->gt($start)
            );

            if ($isAvailable) {
                $slots->push($start->format('H:i'));
            }

            $start->addMinutes($length);
        }

        return $slots;
    }

    /**
     * Showroom-wide: one appointment per slot, regardless of service, since a
     * booking is a meeting time at the store.
     */
    public function isSlotAvailable(Carbon $startAt, ?int $ignoreBookingId = null, bool $lock = false): bool
    {
        $endAt = $startAt->copy()->addMinutes($this->visitMinutes());

        $query = Booking::query()
            ->when($ignoreBookingId, fn ($q) => $q->whereKeyNot($ignoreBookingId))
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        // Inside a DB transaction, lock matching rows so a concurrent request
        // can't slip through between this check and the Booking::create() call.
        if ($lock) {
            $query->lockForUpdate();
        }

        return ! $query->exists();
    }

    public function buildStartAt(string $date, string $time): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}");
    }

    public function buildEndAt(Carbon $startAt): Carbon
    {
        return $startAt->copy()->addMinutes($this->visitMinutes());
    }

    public function isClosedDate(Carbon $date): bool
    {
        return in_array($date->dayOfWeek, $this->closedWeekdays(), true);
    }

    public function closedWeekdays(): array
    {
        return collect(explode(',', (string) setting('BUSINESS_CLOSED_WEEKDAYS', '5')))
            ->map(fn (string $day): string => trim($day))
            ->filter(fn (string $day): bool => $day !== '' && is_numeric($day))
            ->map(fn (string $day): int => (int) $day)
            ->filter(fn (int $day): bool => $day >= 0 && $day <= 6)
            ->unique()
            ->values()
            ->all();
    }
}
