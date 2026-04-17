<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\GalleryItem;
use Livewire\Component;

class GalleryPage extends Component
{
    use SetsSeo;

    public string $activeCategory = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Gallery',
            description: 'Browse our portfolio of car audio installations, window tinting, and custom modifications done at Win Win Car Studio.',
        );
    }

    public function updatedActiveCategory(): void
    {
        // reactive — Livewire handles re-render
    }

    public function render()
    {
        $query = GalleryItem::orderBy('sort_order')->orderBy('created_at', 'desc');

        if ($this->activeCategory !== '') {
            $query->where('category', $this->activeCategory);
        }

        return view('livewire.gallery-page', [
            'items'      => $query->get(),
            'categories' => GalleryItem::CATEGORIES,
        ])->layout('layouts.app');
    }
}
