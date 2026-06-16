<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesOwner;
use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use App\Models\Service;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class BookingForm extends Component
{
    use NotifiesOwner, SetsSeo, UsesSpamProtection;

    public HoneypotData $honeypotData;

    public string $customer_name = '';

    public string $customer_phone = '';

    public string $customer_email = '';

    public string $vehicle_model = '';

    public string $vehicle_plate = '';

    public string $service_id = '';

    public string $preferred_date = '';

    public string $preferred_time = '';

    public string $notes = '';

    public int $currentStep = 1;

    public int $totalSteps = 4;

    public bool $submitted = false;

    public string $reference = '';

    protected function bookingService(): BookingService
    {
        return app(BookingService::class);
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('booking-step');
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('booking-step');
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
            $this->dispatch('booking-step');
        }
    }

    private function validateCurrentStep(): void
    {
        $rules = match ($this->currentStep) {
            // Service is optional — a booking is just a visit. The customer can
            // tell us what it's about, or leave it as a general visit.
            1 => ['service_id' => 'nullable|exists:services,id'],
            2 => ['preferred_date' => 'required|date|after_or_equal:today', 'preferred_time' => 'required|date_format:H:i'],
            // Vehicle model only matters when a specific service is chosen (so we
            // know what we're working on). For a general visit it's optional.
            3 => [
                'vehicle_model' => $this->service_id !== '' ? 'required|min:2|max:120' : 'nullable|max:120',
                'vehicle_plate' => 'nullable|max:30',
            ],
            4 => ['customer_name' => 'required|min:2|max:100', 'customer_phone' => 'required|max:20', 'customer_email' => 'nullable|email|max:100'],
            default => [],
        };
        $this->validate($rules);
    }

    public function getSelectedServiceProperty(): ?Service
    {
        if ($this->service_id === '') {
            return null;
        }

        return Service::find($this->service_id);
    }

    public function mount(?int $service = null): void
    {
        $this->honeypotData = new HoneypotData();

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
            // Required only when a specific service is chosen; optional for a
            // general visit (matches the per-step rule).
            'vehicle_model' => $this->service_id !== '' ? 'required|min:2|max:120' : 'nullable|max:120',
            'vehicle_plate' => 'nullable|max:30',
            'service_id' => 'nullable|exists:services,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'notes' => 'nullable|max:1000',
        ];
    }

    public function getAvailableTimesProperty(): array
    {
        if ($this->preferred_date === '') {
            return [];
        }

        // Slots depend only on the date now — a visit is a fixed-length meeting,
        // independent of which service (if any) the customer is interested in.
        $slots = $this->bookingService()
            ->getAvailableSlots(Carbon::parse($this->preferred_date))
            ->all();

        return array_combine($slots, $slots) ?: [];
    }

    public function submit(): void
    {
        // Honeypot check (field + time gate) — silently rejects bot submissions.
        $this->protectAgainstSpam();

        $ip = request()->ip();
        $throttleKey = 'booking-submit:'.$ip;

        // Burst limit: max 5 booking attempts per IP per 10 minutes.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('customer_phone', __('Too many booking attempts. Please wait :seconds seconds before trying again.', ['seconds' => $seconds]));

            return;
        }

        // Daily cap: stops slow drip-spam that keeps resetting the burst window.
        $dailyKey = 'booking-daily:'.$ip;
        if (RateLimiter::tooManyAttempts($dailyKey, 8)) {
            $this->addError('customer_phone', __('You have reached today’s booking limit. Please WhatsApp us directly instead.'));

            return;
        }

        $this->validate();

        // Service is optional now; a null id is a general "just visiting" booking.
        if ($this->service_id !== '' && ! Service::whereKey($this->service_id)->exists()) {
            $this->addError('service_id', __('That service is no longer available. Please pick another or leave it blank.'));

            return;
        }

        try {
            $date = Carbon::parse($this->preferred_date);
        } catch (\Throwable) {
            $this->addError('preferred_date', __('Please pick a valid date.'));

            return;
        }

        if ($this->bookingService()->isClosedDate($date)) {
            $this->addError('preferred_date', __('We are closed on :days. Please choose another day.', ['days' => $this->closedDaysLabel()]));

            return;
        }

        $startAt = $this->bookingService()->buildStartAt($this->preferred_date, $this->preferred_time);

        if ($startAt->isPast()) {
            $this->addError('preferred_time', __('That time has already passed. Please choose a later slot.'));

            return;
        }

        if (! $this->bookingService()->isSlotAvailable($startAt)) {
            $this->addError('preferred_time', __('This slot is already booked. Please pick another time.'));

            return;
        }

        RateLimiter::hit($throttleKey, 600);   // 10-minute burst window
        RateLimiter::hit($dailyKey, 86400);    // 24-hour daily window

        try {
            $booking = DB::transaction(function () use ($startAt) {
                if (! $this->bookingService()->isSlotAvailable($startAt)) {
                    throw new \RuntimeException(__('This slot is already booked. Please pick another time.'));
                }

                return Booking::create([
                    'reference' => Booking::generateReference(),
                    'customer_name' => strip_tags($this->customer_name),
                    'customer_phone' => strip_tags($this->customer_phone),
                    'customer_email' => $this->customer_email ?: null,
                    'vehicle_model' => strip_tags($this->vehicle_model),
                    'vehicle_plate' => $this->vehicle_plate !== '' ? strtoupper(strip_tags($this->vehicle_plate)) : null,
                    'service_id' => $this->service_id !== '' ? $this->service_id : null,
                    'preferred_date' => $this->preferred_date,
                    'start_at' => $startAt,
                    'end_at' => $this->bookingService()->buildEndAt($startAt),
                    'notes' => strip_tags($this->notes),
                    'status' => 'pending',
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->addError('preferred_time', $e->getMessage());

            return;
        }

        $this->reference = $booking->reference;
        $this->submitted = true;

        $this->notifyOwner(
            'New booking request',
            [
                'Name'    => $booking->customer_name,
                'Phone'   => $booking->customer_phone,
                'Email'   => $booking->customer_email,
                'Vehicle' => trim(($booking->vehicle_model ?? '') . ' ' . ($booking->vehicle_plate ?? '')),
                'When'    => $booking->start_at?->format('D, d M Y · g:i A'),
                'Notes'   => $booking->notes,
            ],
            url('/admin/bookings/' . $booking->getKey() . '/edit'),
            'View booking',
        );

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
        $this->currentStep = 1;
    }

    public function render()
    {
        return view('livewire.booking-form', [
            'services' => Service::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'selectedService' => $this->getSelectedServiceProperty(),
            'businessStart' => setting('BUSINESS_HOURS_START', '09:00'),
            'businessEnd' => setting('BUSINESS_HOURS_END', '18:00'),
            'closedDaysLabel' => $this->closedDaysLabel(),
        ])->layout('layouts.app');
    }

    private function closedDaysLabel(): string
    {
        $names = [
            Carbon::SUNDAY => __('Sundays'),
            Carbon::MONDAY => __('Mondays'),
            Carbon::TUESDAY => __('Tuesdays'),
            Carbon::WEDNESDAY => __('Wednesdays'),
            Carbon::THURSDAY => __('Thursdays'),
            Carbon::FRIDAY => __('Fridays'),
            Carbon::SATURDAY => __('Saturdays'),
        ];

        return collect($this->bookingService()->closedWeekdays())
            ->map(fn (int $day): string => $names[$day] ?? (string) $day)
            ->implode(', ');
    }
}
