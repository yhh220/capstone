<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;

class BookingTracker extends Component
{
    public string $phone    = '';
    public string $token    = '';
    public bool   $searched = false;

    protected array $rules = [
        'phone' => 'nullable|min:6|max:20',
        'token' => 'nullable|min:8|max:100',
    ];

    public function search(): void
    {
        $this->validate();

        if (trim($this->phone) === '' && trim($this->token) === '') {
            $this->addError('phone', __('Enter a phone number or booking token.'));
            return;
        }

        $this->searched = true;
    }

    public function getBookingsProperty()
    {
        if (!$this->searched) {
            return collect();
        }

        if (trim($this->token) !== '') {
            return Booking::with('service')
                ->where('confirm_token', trim($this->token))
                ->get();
        }

        $digits = preg_replace('/\D+/', '', $this->phone);
        if ($digits === '') {
            return collect();
        }

        return Booking::with('service')
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(customer_phone, '-', ''), ' ', ''), '+', ''), '.', '') = ?", [$digits])
            ->orderBy('preferred_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.booking-tracker')->layout('layouts.app');
    }
}
