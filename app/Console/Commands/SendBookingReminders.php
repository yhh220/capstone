<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
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
            try {
                Mail::to($booking->customer_email)->send(new BookingReminderMail($booking));
                $booking->forceFill(['reminder_sent_at' => now()])->save();
                $sent++;
            } catch (\Throwable $e) {
                logger()->error("Booking reminder failed for {$booking->reference}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} booking reminder(s).");

        return self::SUCCESS;
    }
}
