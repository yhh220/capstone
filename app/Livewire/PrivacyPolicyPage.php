<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;

class PrivacyPolicyPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Privacy Policy',
            description: 'Read how Win Win Car Audio handles personal data, booking details, and order information under a PDPA-aligned privacy policy.',
        );
    }

    public function render()
    {
        return view('livewire.privacy-policy-page')->layout('layouts.app');
    }
}
