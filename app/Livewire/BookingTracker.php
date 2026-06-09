<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BookingTracker extends Component
{
    use SetsSeo;

    public string $phone    = '';
    public string $token    = '';
    public bool   $searched = false;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Track My Booking',
            description: 'Check the status of your service booking at Win Win Car Audio. Enter your phone number or booking token to get updates.',
        );
    }

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

        // Rate limit to stop brute-force phone-number enumeration / PII harvesting.
        // (Token lookups are unguessable UUIDs; phone lookups are the risk.)
        $key = 'booking-track:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('phone', __('Too many lookups. Please try again in :seconds seconds.', ['seconds' => $seconds]));
            return;
        }
        RateLimiter::hit($key, 120);

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
