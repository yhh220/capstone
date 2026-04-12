<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;

class BookingTracker extends Component
{
    public string $phone    = '';
    public bool   $searched = false;

    protected array $rules = [
        'phone' => 'required|min:6|max:20',
    ];

    public function search(): void
    {
        $this->validate();
        $this->searched = true;
    }

    public function getBookingsProperty()
    {
        if (!$this->searched || $this->phone === '') {
            return collect();
        }

        return Booking::where('customer_phone', $this->phone)
            ->with('service')
            ->orderBy('preferred_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.booking-tracker')->layout('layouts.app');
    }
}
