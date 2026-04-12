<?php

namespace App\Livewire;

use App\Models\GalleryItem;
use Livewire\Component;

class GalleryPage extends Component
{
    public string $activeCategory = '';

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
