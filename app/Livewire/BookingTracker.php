<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BookingTracker extends Component
{
    use SetsSeo;

    public string  $reference = '';
    public string  $email     = '';
    public ?Booking $booking  = null;
    public bool    $searched  = false;
    public string  $errorMsg  = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Track My Booking',
            description: 'Check or cancel your service booking at Win Win Car Audio. Enter your booking reference and email.',
        );
    }

    public function search(): void
    {
        $this->validate([
            'reference' => 'required|string|max:50',
            'email'     => 'required|email|max:100',
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

        $booking = $this->findBookingByReference();

        if (! $booking) {
            $this->errorMsg = __('No booking found. Please check your booking reference.');
            return;
        }

        if (strtolower(trim($booking->customer_email ?? '')) !== strtolower(trim($this->email))) {
            $this->errorMsg = __('Email does not match. Please check your email address.');
            return;
        }

        $this->booking = $booking;
    }

    public function cancelBooking(): void
    {
        $booking = $this->findBookingByReference();

        if (! $booking) {
            $this->errorMsg = __('No booking found. Please check your booking reference.');
            return;
        }

        if (strtolower(trim($booking->customer_email ?? '')) !== strtolower(trim($this->email))) {
            $this->errorMsg = __('Email does not match. Please check your email address.');
            return;
        }

        if (! in_array($booking->status, ['cancelled', 'completed'], true)) {
            $booking->update(['status' => 'cancelled']);
        }

        $this->booking = $booking->fresh('service');
        session()->flash('success', __('Your booking has been cancelled.'));
    }

    private function findBookingByReference(): ?Booking
    {
        $reference = trim($this->reference);

        if ($reference === '') {
            return null;
        }

        return Booking::with('service')
            ->where('reference', $reference)
            ->first();
    }

    public function render()
    {
        return view('livewire.booking-tracker')->layout('layouts.app');
    }
}
