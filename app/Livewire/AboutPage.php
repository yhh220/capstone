<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;

class AboutPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'About Us',
            description: 'Learn about Win Win Car Studio — a showroom-first brand focused on trust, expert advice, and hands-on product experience in Kuala Lumpur.',
        );
    }

    public function render()
    {
        return view('livewire.about-page')->layout('layouts.app');
    }
}
