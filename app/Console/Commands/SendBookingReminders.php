<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Email a day-before reminder for upcoming bookings (skips cancelled/completed and already-reminded).';

    public function handle(): int
    {
        $bookings = Booking::query()
            // Only active, upcoming bookings — cancelled/completed never get a reminder.
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('reminder_sent_at')          // at most once
            ->whereNotNull('customer_email')         // nothing to send to otherwise
            ->whereBetween('start_at', [
                now()->addDay()->startOfDay(),
                now()->addDay()->endOfDay(),
            ])
            ->with('service')
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            // Re-fetch under a row lock and re-check reminder_sent_at so two
            // overlapping command runs can't both send the same reminder.
            $claimed = DB::transaction(function () use ($booking) {
                $fresh = Booking::where('id', $booking->id)
                    ->whereNull('reminder_sent_at')
                    ->lockForUpdate()
                    ->first();

                if (! $fresh) {
                    return false;
                }

                $fresh->forceFill(['reminder_sent_at' => now()])->save();

                return true;
            });

            if (! $claimed) {
                continue;
            }

            try {
                Mail::to($booking->customer_email)->send(new BookingReminderMail($booking));
                $sent++;
            } catch (\Throwable $e) {
                logger()->error("Booking reminder failed for {$booking->reference}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} booking reminder(s).");

        return self::SUCCESS;
    }
}
