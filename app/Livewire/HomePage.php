<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class HomePage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $storeName = config('services.store.name', 'Win Win Car Studio');
        $this->setSeo(
            title: $storeName,
            description: 'Browse car audio, window tint, and accessories online. Visit our showroom in Kuala Lumpur or chat on WhatsApp for expert advice and installation.',
        );
    }

    public function render()
    {
        if (!Schema::hasTable('products') || !Schema::hasTable('categories')) {
            return view('livewire.home-page', [
                'featuredProducts' => new Collection(),
                'categories' => new Collection(),
                'newArrivals' => new Collection(),
                'testimonials' => new Collection(),
                'showcaseProduct' => null,
                'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
            ])->layout('layouts.app');
        }

        $testimonials = Feedback::where('is_active', true)
            ->orderBy('sort_order')
            ->latest('id')
            ->take(6)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $showcaseProduct = Product::where('is_active', true)
            ->where('has_3d', true)
            ->whereNotNull('model_url')
            ->latest()
            ->first();

        $newArrivals = Product::where('is_active', true)
            ->whereNotIn('id', $featuredProducts->pluck('id'))
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.home-page', [
            'featuredProducts' => $featuredProducts,
            'categories' => Category::where('is_active', true)
                ->withCount('products')
                ->take(6)
                ->get(),
            'newArrivals' => $newArrivals,
            'testimonials' => $testimonials,
            'showcaseProduct' => $showcaseProduct,
            'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
        ])->layout('layouts.app');
    }
}
