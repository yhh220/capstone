<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesOwner;
use App\Livewire\Concerns\SetsSeo;
use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BookingTracker extends Component
{
    use NotifiesOwner, SetsSeo;

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
        $cancelKey = 'booking-cancel:' . request()->ip();
        if (RateLimiter::tooManyAttempts($cancelKey, 3)) {
            $seconds = RateLimiter::availableIn($cancelKey);
            $this->errorMsg = __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]);
            return;
        }
        RateLimiter::hit($cancelKey, 120);

        $booking = $this->findBookingByReference();

        if (! $booking) {
            $this->errorMsg = __('No booking found. Please check your booking reference.');
            return;
        }

        if (strtolower(trim($booking->customer_email ?? '')) !== strtolower(trim($this->email))) {
            $this->errorMsg = __('Email does not match. Please check your email address.');
            return;
        }

        if (in_array($booking->status, ['cancelled', 'completed'], true)) {
            session()->flash('success', __('This booking has already been cancelled or completed.'));
            $this->booking = $booking->fresh('service');
            return;
        }

        $booking->update(['status' => 'cancelled']);
        $this->booking = $booking->fresh('service');
        session()->flash('success', __('Your booking has been cancelled.'));

        // Notify the customer (if email given)
        try {
            if ($booking->customer_email) {
                Mail::to($booking->customer_email)->send(new BookingCancelledMail($this->booking));
            }
        } catch (\Throwable $e) {
            logger()->error('BookingCancelledMail failed: ' . $e->getMessage());
        }

        // Notify the shop owner
        $this->notifyOwner(
            'Booking cancelled by customer',
            [
                'Reference' => $booking->reference,
                'Customer'  => $booking->customer_name,
                'Phone'     => $booking->customer_phone,
                'When'      => $booking->start_at?->format('D, d M Y · g:i A'),
            ],
            url('/admin/bookings/' . $booking->getKey() . '/edit'),
            'View booking',
        );
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
