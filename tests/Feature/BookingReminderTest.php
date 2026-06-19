<?php

namespace Tests\Feature;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingReminderTest extends TestCase
{
    use RefreshDatabase;

    private function booking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'reference'      => Booking::generateReference(),
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'preferred_date' => now()->addDay()->toDateString(),
            'start_at'       => now()->addDay()->setTime(10, 0),
            'end_at'         => now()->addDay()->setTime(11, 0),
            'status'         => 'pending',
        ], $overrides));
    }

    public function test_it_reminds_tomorrows_active_booking_exactly_once(): void
    {
        Mail::fake();
        $booking = $this->booking();

        $this->artisan('bookings:send-reminders')->assertSuccessful();

        Mail::assertSent(BookingReminderMail::class, 1);
        $this->assertNotNull($booking->fresh()->reminder_sent_at);

        // A second run must not re-send (reminder_sent_at guard).
        Mail::fake();
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_it_skips_cancelled_completed_no_email_and_far_future(): void
    {
        Mail::fake();
        $this->booking(['status' => 'cancelled']);
        $this->booking(['status' => 'completed']);
        $this->booking(['customer_email' => null]);
        $this->booking([
            'preferred_date' => now()->addWeek()->toDateString(),
            'start_at'       => now()->addWeek()->setTime(10, 0),
        ]);

        $this->artisan('bookings:send-reminders')->assertSuccessful();

        Mail::assertNothingSent();
    }
}
