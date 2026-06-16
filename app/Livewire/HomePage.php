<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Brand;
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
            description: 'Browse car audio, window tint, and accessories online. Visit our showroom in Shah Alam or chat on WhatsApp for expert advice and installation.',
        );
    }

    public function render()
    {
        static $hasProductsTable, $hasCategoriesTable, $hasBrandsTable, $hasFeedbackTable;
        $hasProductsTable    ??= Schema::hasTable('products');
        $hasCategoriesTable  ??= Schema::hasTable('categories');
        $hasBrandsTable      ??= Schema::hasTable('brands');
        $hasFeedbackTable    ??= Schema::hasTable('feedback');

        if (!$hasProductsTable || !$hasCategoriesTable) {
            return view('livewire.home-page', [
                'categories'      => new Collection(),
                'featuredProducts' => new Collection(),
                'testimonials'    => new Collection(),
                'showcaseProduct' => null,
                'brands'          => new Collection(),
                'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
            ])->layout('layouts.app');
        }

        $brands = $hasBrandsTable
            ? Brand::where('is_active', true)->orderBy('sort_order')->get()
            : new Collection();

        $testimonials = $hasFeedbackTable
            ? Feedback::where('is_active', true)
                ->orderBy('sort_order')
                ->take(12)
                ->get()
            : new Collection();

        $showcaseProduct = Product::where('is_active', true)
            ->where('has_3d', true)
            ->whereNotNull('model_url')
            ->latest()
            ->first();

        // Products the admin flagged "Show on Homepage" (is_featured). Falls back
        // to nothing if none are flagged — the section hides itself.
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.home-page', [
            'categories'      => Category::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(6)
                ->get(),
            'featuredProducts' => $featuredProducts,
            'testimonials'    => $testimonials,
            'showcaseProduct' => $showcaseProduct,
            'brands'          => $brands,
            'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
        ])->layout('layouts.app');
    }
}
