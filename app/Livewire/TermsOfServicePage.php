<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;

class TermsOfServicePage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Terms of Service',
            description: 'Review the booking, showroom, order, and product enquiry terms for Win Win Car Audio Auto Accessories.',
        );
    }

    public function render()
    {
        return view('livewire.terms-of-service-page')->layout('layouts.app');
    }
}
