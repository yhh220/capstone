<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductDetail extends Component
{
    public Product $product;
    public int $quantity = 1;

    public function mount(string $slug)
    {
        $this->product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function incrementQty(): void
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('livewire.product-detail', [
            'related' => Product::where('category_id', $this->product->category_id)
                ->where('id', '!=', $this->product->id)
                ->where('is_active', true)
                ->take(4)->get(),
        ])->layout('layouts.app');
    }
}
