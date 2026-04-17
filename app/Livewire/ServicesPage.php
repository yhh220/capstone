<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Service;
use Livewire\Component;

class ServicesPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Our Services',
            description: 'Professional car audio installation, window tinting, and modification services. Book an appointment online or enquire on WhatsApp.',
        );
    }

    public function render()
    {
        return view('livewire.services-page', [
            'services' => Service::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ])->layout('layouts.app');
    }
}
