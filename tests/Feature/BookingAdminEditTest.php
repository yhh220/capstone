<?php

namespace Tests\Feature;

use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class BookingAdminEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * The shop is closed on Fridays (seeded BUSINESS_CLOSED_WEEKDAYS=5) — a bare
     * now()->addDays($n) lands on a closed day on whichever Thursday this runs,
     * making EditBooking::handleRecordUpdate() halt before sending any email.
     * Walk forward past Friday so these dates are always open.
     */
    private function openDay(int $daysAhead): Carbon
    {
        $date = now()->addDays($daysAhead);

        while ($date->dayOfWeek === Carbon::FRIDAY) {
            $date->addDay();
        }

        return $date;
    }

    private function booking(array $overrides = []): Booking
    {
        $day = $this->openDay(1);

        return Booking::create(array_merge([
            'reference' => Booking::generateReference(),
            'customer_name' => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'preferred_date' => $day->toDateString(),
            'start_at' => $day->copy()->setTime(10, 0),
            'end_at' => $day->copy()->setTime(11, 0),
            'status' => 'pending',
        ], $overrides));
    }

    public function test_editing_status_to_confirmed_emails_the_customer(): void
    {
        Mail::fake();
        $this->actingAs($this->admin(), 'admin');
        $booking = $this->booking();

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm(['status' => 'confirmed'])
            ->call('save')
            ->assertHasNoFormErrors();

        Mail::assertSent(BookingConfirmedMail::class, 1);
        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_editing_status_to_cancelled_emails_the_customer(): void
    {
        Mail::fake();
        $this->actingAs($this->admin(), 'admin');
        $booking = $this->booking(['status' => 'confirmed']);

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm(['status' => 'cancelled'])
            ->call('save')
            ->assertHasNoFormErrors();

        Mail::assertSent(BookingCancelledMail::class, 1);
    }

    public function test_saving_without_a_status_change_sends_no_email(): void
    {
        Mail::fake();
        $this->actingAs($this->admin(), 'admin');
        $booking = $this->booking();

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm(['notes' => 'Customer called to confirm vehicle details.'])
            ->call('save')
            ->assertHasNoFormErrors();

        Mail::assertNothingSent();
    }

    public function test_rescheduling_onto_an_already_booked_slot_is_blocked(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $conflictDay = $this->openDay(2);
        $this->booking([
            'start_at' => $conflictDay->copy()->setTime(14, 0),
            'end_at' => $conflictDay->copy()->setTime(15, 0),
            'status' => 'confirmed',
        ]);
        $booking = $this->booking();
        $originalDay = $booking->start_at->copy();

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm([
                'start_at' => $conflictDay->copy()->setTime(14, 0)->toDateTimeString(),
                'end_at' => $conflictDay->copy()->setTime(15, 0)->toDateTimeString(),
            ])
            ->call('save');

        // The conflicting slot must not have been saved onto this booking.
        $this->assertTrue($booking->fresh()->start_at->isSameDay($originalDay));
    }

    public function test_rescheduling_clears_a_previously_sent_reminder(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $booking = $this->booking(['reminder_sent_at' => now()]);
        $newDay = $this->openDay(5);

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm([
                'start_at' => $newDay->copy()->setTime(10, 0)->toDateTimeString(),
                'end_at' => $newDay->copy()->setTime(11, 0)->toDateTimeString(),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($booking->fresh()->reminder_sent_at);
    }
}
