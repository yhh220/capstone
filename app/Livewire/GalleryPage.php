<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\GalleryItem;
use Livewire\Component;
use Livewire\WithPagination;

class GalleryPage extends Component
{
    use SetsSeo, WithPagination;

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
        $this->resetPage();
    }

    public function render()
    {
        $query = GalleryItem::orderBy('sort_order')->orderBy('created_at', 'desc');

        if ($this->activeCategory !== '') {
            $query->where('category', $this->activeCategory);
        }

        return view('livewire.gallery-page', [
            'items'      => $query->paginate(24),
            'categories' => GalleryItem::CATEGORIES,
        ])->layout('layouts.app');
    }
}
