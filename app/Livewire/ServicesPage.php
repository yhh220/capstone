<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;

class ServicesPage extends Component
{
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
