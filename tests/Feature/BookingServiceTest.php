<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_adjacent_booking_starting_at_previous_end_is_available(): void
    {
        $service = Service::create([
            'name' => 'Audio Installation',
            'description' => 'Install audio system',
            'duration_minutes' => 60,
            'buffer_after' => 15,
            'is_active' => true,
        ]);

        Booking::create([
            'customer_name' => 'Existing Customer',
            'customer_phone' => '0123456789',
            'service_id' => $service->id,
            'preferred_date' => '2026-06-01',
            'start_at' => Carbon::parse('2026-06-01 10:00'),
            'end_at' => Carbon::parse('2026-06-01 11:15'),
            'status' => 'confirmed',
        ]);

        $bookingService = app(BookingService::class);

        // Booking is now showroom-wide (one appointment per slot, no per-service
        // duration), so isSlotAvailable() takes just the start time.
        $this->assertTrue($bookingService->isSlotAvailable(Carbon::parse('2026-06-01 11:15')));
        $this->assertFalse($bookingService->isSlotAvailable(Carbon::parse('2026-06-01 11:00')));
    }

    public function test_opening_hours_label_follows_the_settings(): void
    {
        \App\Models\Setting::setValue('BUSINESS_HOURS_START', '10:30');
        \App\Models\Setting::setValue('BUSINESS_HOURS_END', '20:00');
        \App\Models\Setting::setValue('BUSINESS_CLOSED_WEEKDAYS', '5');

        $label = app(BookingService::class)->openingHoursLabel('en');

        // Times and the closed day come straight from the settings the booking
        // calendar enforces — this is what the footer/contact page/chatbot show.
        $this->assertStringContainsString('10:30 AM', $label);
        $this->assertStringContainsString('8:00 PM', $label);
        $this->assertStringContainsString('Friday', $label);

        // No closed days → the "open daily" variant.
        \App\Models\Setting::setValue('BUSINESS_CLOSED_WEEKDAYS', '');
        $this->assertStringContainsString('daily', app(BookingService::class)->openingHoursLabel('en'));
    }
}
