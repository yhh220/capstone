<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;

class FaqPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'FAQ',
            description: 'Find quick answers about bookings, showroom visits, compatibility, and online shopping at Win Win Car Audio.',
        );
    }

    public function render()
    {
        return view('livewire.faq-page')->layout('layouts.app');
    }
}
