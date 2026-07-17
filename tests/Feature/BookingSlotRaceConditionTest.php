<?php

namespace Tests\Feature;

use App\Livewire\BookingForm;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression test: isSlotAvailable(..., lock: true) takes lockForUpdate() on
 * rows matching the slot, but that can't block a second request racing for a
 * slot with NO existing row yet — there's nothing to lock until one request
 * actually inserts. BookingForm::submit() now wraps the check-then-create in
 * a Cache::lock keyed by the exact start time, so a concurrent attempt at the
 * same never-before-booked slot is rejected instead of silently double-booking.
 */
class BookingSlotRaceConditionTest extends TestCase
{
    use RefreshDatabase;

    private function fillValidBooking($component, Carbon $startAt)
    {
        return $component
            ->set('customer_name', 'Jane Doe')
            ->set('customer_phone', '0123456789')
            ->set('customer_email', 'jane@example.test')
            ->set('preferred_date', $startAt->toDateString())
            ->set('preferred_time', $startAt->format('H:i'));
    }

    public function test_submit_rejects_a_slot_someone_else_is_mid_booking_for(): void
    {
        $startAt = Carbon::parse('next monday')->setTime(10, 0);

        // Simulate another request that's already past the slot-availability
        // check and is mid-transaction inserting its own booking for this slot —
        // no row exists yet, so a plain lockForUpdate() check can't see it.
        $lock = Cache::lock('booking-slot:'.$startAt->toDateTimeString(), 10);
        $lock->get();

        // Mount first so the honeypot's valid_from is stamped before we fast-forward —
        // travelling first would make the component think it was filled instantly.
        $component = Livewire::test(BookingForm::class);
        $this->travel(2)->seconds(); // clear the honeypot's minimum-fill-time gate

        $this->fillValidBooking($component, $startAt)->call('submit');

        $component->assertHasErrors(['preferred_time']);
        $this->assertSame(0, Booking::count());

        $lock->release();
    }

    public function test_submit_succeeds_once_the_competing_lock_is_released(): void
    {
        $startAt = Carbon::parse('next monday')->setTime(11, 0);

        $component = Livewire::test(BookingForm::class);
        $this->travel(2)->seconds();

        $this->fillValidBooking($component, $startAt)->call('submit');

        $component->assertHasNoErrors(['preferred_time']);
        $this->assertSame(1, Booking::count());
    }
}
