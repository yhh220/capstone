<?php

namespace Tests\Feature;

use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Models\Booking;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression guard: the admin Confirm action used to blindly update the row,
 * so a click racing a customer cancellation resurrected the cancelled booking
 * (whose slot may already have been re-booked) and emailed a confirmation for
 * it. Confirm must be an atomic pending→confirmed claim.
 */
class BookingConfirmGuardTest extends TestCase
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
            'end_at'         => now()->addDay()->setTime(10, 30),
            'status'         => 'pending',
        ], $overrides));
    }

    private function actAsAdmin(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create(['role' => 'admin']), 'admin');
    }

    public function test_a_cancelled_booking_cannot_be_confirmed(): void
    {
        $booking = $this->booking(['status' => 'cancelled']);
        $this->actAsAdmin();

        Livewire::test(ListBookings::class)
            ->assertTableActionHidden('confirmBooking', $booking);

        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_a_pending_booking_confirms_normally(): void
    {
        $booking = $this->booking();
        $this->actAsAdmin();

        Livewire::test(ListBookings::class)
            ->callTableAction('confirmBooking', $booking);

        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_bulk_confirm_skips_non_pending_rows(): void
    {
        $pending   = $this->booking();
        $cancelled = $this->booking(['status' => 'cancelled', 'start_at' => now()->addDay()->setTime(11, 0), 'end_at' => now()->addDay()->setTime(11, 30)]);
        $this->actAsAdmin();

        Livewire::test(ListBookings::class)
            ->callTableBulkAction('confirm', [$pending, $cancelled]);

        $this->assertSame('confirmed', $pending->fresh()->status);
        $this->assertSame('cancelled', $cancelled->fresh()->status, 'A cancelled booking must never be resurrected by bulk confirm.');
    }
}
