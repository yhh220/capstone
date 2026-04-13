<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use App\Models\Service;
use Livewire\Component;

class BookingForm extends Component
{
    use SetsSeo;
    public string $customer_name    = '';
    public string $customer_phone   = '';
    public string $customer_email   = '';
    public string $service_id       = '';
    public string $preferred_date   = '';
    public string $preferred_time   = '';
    public string $notes            = '';

    /** Pre-selected service from URL query param */
    public function mount(?int $service = null): void
    {
        if ($service) {
            $this->service_id = (string) $service;
        }

        $this->setSeo(
            title: 'Book an Appointment',
            description: 'Book a car audio installation, window tint, or modification service at Win Win Car Studio. Choose your date and time online.',
        );
    }

    protected array $rules = [
        'customer_name'  => 'required|min:2|max:100',
        'customer_phone' => 'required|max:20',
        'customer_email' => 'nullable|email|max:100',
        'service_id'     => 'required|exists:services,id',
        'preferred_date' => 'required|date|after_or_equal:today',
        'preferred_time' => 'required',
        'notes'          => 'nullable|max:1000',
    ];

    public function getAvailableTimesProperty(): array
    {
        $times = [];
        for ($h = 10; $h <= 19; $h++) {
            foreach (['00', '30'] as $m) {
                if ($h === 10 && $m === '00') continue;
                $key = sprintf('%02d:%s', $h, $m);
                $times[$key] = $key;
            }
        }
        return $times;
    }

    public function submit(): void
    {
        $this->validate();

        Booking::create([
            'customer_name'  => strip_tags($this->customer_name),
            'customer_phone' => strip_tags($this->customer_phone),
            'customer_email' => $this->customer_email ?: null,
            'service_id'     => $this->service_id,
            'preferred_date' => $this->preferred_date,
            'preferred_time' => $this->preferred_time,
            'notes'          => strip_tags($this->notes),
            'status'         => 'pending',
        ]);

        $this->reset(['customer_name', 'customer_phone', 'customer_email', 'service_id', 'preferred_date', 'preferred_time', 'notes']);
        session()->flash('booking_success', true);
    }

    public function render()
    {
        return view('livewire.booking-form', [
            'services' => Service::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
