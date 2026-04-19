<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProductDetail extends Component
{
    use SetsSeo;

    public Product $product;
    public int $quantity = 1;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $imageUrl = $this->product->image ? Storage::url($this->product->image) : null;
        $description = $this->product->short_description
            ?: 'View details and enquire about ' . $this->product->name . ' at Win Win Car Studio. Visit our showroom or chat on WhatsApp.';

        $this->setSeo(
            title: $this->product->name,
            description: $description,
            imageUrl: $imageUrl ? url($imageUrl) : null,
        );
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < ($this->product->stock ?: 99)) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        CartPage::addToCart($this->product->id, $this->quantity);
        session()->flash('success', __('Added to cart!'));
        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.product-detail', [
            'related' => Product::where('category_id', $this->product->category_id)
                ->where('id', '!=', $this->product->id)
                ->where('is_active', true)
                ->take(4)
                ->get(),
            'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
        ])->layout('layouts.app');
    }
}
