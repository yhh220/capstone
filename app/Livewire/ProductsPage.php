<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';

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
            ->with('category')
            ->latest();

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category !== '') {
            $query->where('category_id', $this->category);
        }

        return view('livewire.products-page', [
            'products' => $query->paginate(12),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
