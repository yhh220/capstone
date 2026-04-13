<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPage extends Component
{
    use SetsSeo, WithPagination;

    public string $search   = '';
    public string $category = '';
    public string $minPrice = '';
    public string $maxPrice = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Our Products',
            description: 'Browse our full range of car audio, window tint, and accessories. Filter by category and enquire on WhatsApp.',
        );
    }

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedMinPrice(): void { $this->resetPage(); }
    public function updatedMaxPrice(): void { $this->resetPage(); }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->with('category')
            ->latest();

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category !== '') {
            $query->where('category_id', $this->category);
        }

        if ($this->minPrice !== '' && is_numeric($this->minPrice)) {
            $query->where('price', '>=', (float) $this->minPrice);
        }

        if ($this->maxPrice !== '' && is_numeric($this->maxPrice)) {
            $query->where('price', '<=', (float) $this->maxPrice);
        }

        return view('livewire.products-page', [
            'products' => $query->paginate(12),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
