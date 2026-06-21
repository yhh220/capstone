<?php

namespace Tests\Feature;

use App\Livewire\BookingForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingFormEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_form_requires_customer_email(): void
    {
        Livewire::test(BookingForm::class)
            ->set('currentStep', 4)
            ->set('customer_name', 'Jane Doe')
            ->set('customer_phone', '0123456789')
            ->set('customer_email', '')
            ->call('nextStep')
            ->assertHasErrors(['customer_email' => 'required']);
    }

    public function test_booking_form_accepts_a_valid_customer_email(): void
    {
        Livewire::test(BookingForm::class)
            ->set('currentStep', 4)
            ->set('customer_name', 'Jane Doe')
            ->set('customer_phone', '0123456789')
            ->set('customer_email', 'jane@example.test')
            ->call('nextStep')
            ->assertHasNoErrors(['customer_email']);
    }
}
