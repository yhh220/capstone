<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class HomePage extends Component
{
    private function fallbackTestimonials(): Collection
    {
        return collect([
            ['name' => 'Ahmad Rizal', 'location' => 'KL', 'message' => "The staff explained the options clearly on WhatsApp before I came over. The showroom visit was smooth and helpful.", 'rating' => 5],
            ['name' => 'Siti Nurul', 'location' => 'Selangor', 'message' => 'Very helpful team and excellent product guidance. I could compare models in person before deciding.', 'rating' => 5],
            ['name' => 'Tan Wei Ming', 'location' => 'Penang', 'message' => 'I liked that the website showed the products first, then the store team helped me choose the right fit.', 'rating' => 5],
        ]);
    }

    public function render()
    {
        if (!Schema::hasTable('products') || !Schema::hasTable('categories')) {
            return view('livewire.home-page', [
                'featuredProducts' => new Collection(),
                'categories' => new Collection(),
                'newArrivals' => new Collection(),
                'testimonials' => $this->fallbackTestimonials(),
            ])->layout('layouts.app');
        }

        $testimonials = $this->fallbackTestimonials();

        if (Schema::hasTable('feedback')) {
            $feedback = Feedback::where('is_active', true)
                ->orderBy('sort_order')
                ->latest('id')
                ->take(6)
                ->get();

            if ($feedback->isNotEmpty()) {
                $testimonials = $feedback;
            }
        }

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

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
        ])->layout('layouts.app');
    }
}
