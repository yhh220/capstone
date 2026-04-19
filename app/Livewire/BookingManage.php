<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use Livewire\Component;

class BookingManage extends Component
{
    use SetsSeo;

    public Booking $booking;

    public function mount(string $token): void
    {
        $this->booking = Booking::with('service')
            ->where('confirm_token', $token)
            ->firstOrFail();

        $this->setSeo(
            title: 'Manage Booking',
            description: 'Review your booking details, status, and contact information for your appointment at Win Win Car Audio.',
        );
    }

    public function cancelBooking(): void
    {
        if (!in_array($this->booking->status, ['cancelled', 'completed'], true)) {
            $this->booking->update(['status' => 'cancelled']);
            $this->booking->refresh();
            session()->flash('success', __('Your booking has been cancelled.'));
        }
    }

    public function render()
    {
        return view('livewire.booking-manage')->layout('layouts.app');
    }
}
