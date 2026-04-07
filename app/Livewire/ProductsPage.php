<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $sortBy = 'latest';
    public ?float $minPrice = null;
    public ?float $maxPrice = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->with('category');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        match($this->sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return view('livewire.products-page', [
            'products' => $query->paginate(12),
            'categories' => Category::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
