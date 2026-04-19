<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use App\Models\Service;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Livewire\Component;

class BookingForm extends Component
{
    use SetsSeo;

    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_email = '';
    public string $vehicle_model = '';
    public string $vehicle_plate = '';
    public string $service_id = '';
    public string $preferred_date = '';
    public string $preferred_time = '';
    public string $notes = '';

    protected function bookingService(): BookingService
    {
        return app(BookingService::class);
    }

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

    protected function rules(): array
    {
        return [
            'customer_name' => 'required|min:2|max:100',
            'customer_phone' => 'required|max:20',
            'customer_email' => 'nullable|email|max:100',
            'vehicle_model' => 'required|min:2|max:120',
            'vehicle_plate' => 'nullable|max:30',
            'service_id' => 'required|exists:services,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required',
            'notes' => 'nullable|max:1000',
        ];
    }

    public function getAvailableTimesProperty(): array
    {
        if ($this->service_id === '' || $this->preferred_date === '') {
            return [];
        }

        $service = Service::find($this->service_id);

        if (!$service) {
            return [];
        }

        $slots = $this->bookingService()
            ->getAvailableSlots($service, Carbon::parse($this->preferred_date))
            ->all();

        return array_combine($slots, $slots) ?: [];
    }

    public function submit(): void
    {
        $this->validate();

        $service = Service::findOrFail($this->service_id);

        try {
            $date = Carbon::parse($this->preferred_date);
        } catch (\Throwable) {
            $this->addError('preferred_date', __('Please pick a valid date.'));
            return;
        }

        if ($date->dayOfWeek === Carbon::FRIDAY) {
            $this->addError('preferred_date', __('We are closed on Fridays. Please choose another day.'));
            return;
        }

        $startAt = $this->bookingService()->buildStartAt($this->preferred_date, $this->preferred_time);

        if ($startAt->isPast()) {
            $this->addError('preferred_time', __('That time has already passed. Please choose a later slot.'));
            return;
        }

        if (!$this->bookingService()->isSlotAvailable($service, $startAt)) {
            $this->addError('preferred_time', __('This slot is already booked. Please pick another time.'));
            return;
        }

        $booking = Booking::create([
            'customer_name' => strip_tags($this->customer_name),
            'customer_phone' => strip_tags($this->customer_phone),
            'customer_email' => $this->customer_email ?: null,
            'vehicle_model' => strip_tags($this->vehicle_model),
            'vehicle_plate' => $this->vehicle_plate !== '' ? strtoupper(strip_tags($this->vehicle_plate)) : null,
            'service_id' => $this->service_id,
            'preferred_date' => $this->preferred_date,
            'preferred_time' => $this->preferred_time,
            'start_at' => $startAt,
            'end_at' => $this->bookingService()->buildEndAt($service, $startAt),
            'notes' => strip_tags($this->notes),
            'status' => 'pending',
            'confirm_token' => (string) str()->uuid(),
        ]);

        $this->reset([
            'customer_name',
            'customer_phone',
            'customer_email',
            'vehicle_model',
            'vehicle_plate',
            'service_id',
            'preferred_date',
            'preferred_time',
            'notes',
        ]);

        session()->flash('booking_success', $booking->manage_url);
    }

    public function render()
    {
        return view('livewire.booking-form', [
            'services' => Service::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'businessStart' => setting('BUSINESS_HOURS_START', '09:00'),
            'businessEnd' => setting('BUSINESS_HOURS_END', '18:00'),
        ])->layout('layouts.app');
    }
}
