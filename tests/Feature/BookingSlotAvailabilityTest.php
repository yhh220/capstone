<?php

namespace Tests\Feature;

use App\Services\Booking\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Same-day slot filtering + the timezone default it depends on.
 *
 * Regression guards: the app ran on UTC (Laravel default) for a Malaysian
 * store, so "past" checks trailed local reality by 8 hours; and picking
 * "today" listed the entire business day — hours already gone included —
 * with the customer only rejected at the final submit step.
 */
class BookingSlotAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_runs_on_malaysia_time(): void
    {
        $this->assertSame('Asia/Kuala_Lumpur', config('app.timezone'));
    }

    public function test_todays_slot_list_excludes_hours_already_gone(): void
    {
        // Wednesday 2026-07-08, 14:05 local — mid-afternoon on an open day.
        $this->travelTo(Carbon::create(2026, 7, 8, 14, 5));

        $slots = app(BookingService::class)->getAvailableSlots(Carbon::today());

        $this->assertSame('14:30', $slots->first(), 'The first offered slot must be in the future.');
        $this->assertFalse($slots->contains('09:00'), 'Morning slots are already gone by mid-afternoon.');
        $this->assertSame('17:30', $slots->last());
    }

    public function test_future_dates_still_offer_the_full_day(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 8, 14, 5));

        $slots = app(BookingService::class)->getAvailableSlots(Carbon::tomorrow());

        $this->assertSame('09:00', $slots->first());
        $this->assertCount(18, $slots); // 09:00–18:00 in 30-minute steps
    }
}
