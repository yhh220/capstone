<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BookingTracker extends Component
{
    use SetsSeo;

    public string $reference = '';
    public string $phone     = '';
    public ?Booking $booking = null;
    public bool   $searched  = false;
    public string $errorMsg  = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Track My Booking',
            description: 'Check or cancel your service booking at Win Win Car Audio. Enter your booking reference and phone number.',
        );
    }

    /**
     * Look up a single booking by reference + phone. Requiring BOTH (like the
     * order tracker's order number + email) means a reference alone is useless
     * and there's no way to enumerate bookings by phone number.
     */
    public function search(): void
    {
        $this->validate([
            'reference' => 'required|string|max:50',
            'phone'     => 'required|string|min:6|max:20',
        ]);

        $this->searched = true;
        $this->errorMsg = '';
        $this->booking  = null;

        $key = 'booking-track:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $seconds = RateLimiter::availableIn($key);
            $this->errorMsg = __('Too many lookups. Please try again in :seconds seconds.', ['seconds' => $seconds]);
            return;
        }
        RateLimiter::hit($key, 120);

        $this->booking = $this->findBooking();

        if (! $this->booking) {
            $this->errorMsg = __('No booking found. Please check your reference and phone number.');
        } else {
            RateLimiter::clear($key);
        }
    }

    public function cancelBooking(): void
    {
        // Re-verify reference + phone server-side before mutating anything.
        $booking = $this->findBooking();

        if (! $booking) {
            $this->errorMsg = __('No booking found. Please check your reference and phone number.');
            return;
        }

        if (! in_array($booking->status, ['cancelled', 'completed'], true)) {
            $booking->update(['status' => 'cancelled']);
        }

        $this->booking = $booking->fresh('service');
        session()->flash('success', __('Your booking has been cancelled.'));
    }

    private function findBooking(): ?Booking
    {
        $reference = trim($this->reference);
        $digits    = preg_replace('/\D+/', '', $this->phone);

        if ($reference === '' || $digits === '') {
            return null;
        }

        return Booking::with('service')
            ->where('reference', $reference)
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(customer_phone, '-', ''), ' ', ''), '+', ''), '.', '') = ?", [$digits])
            ->first();
    }

    public function render()
    {
        return view('livewire.booking-tracker')->layout('layouts.app');
    }
}
