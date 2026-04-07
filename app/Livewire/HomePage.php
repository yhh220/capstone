<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class HomePage extends Component
{
    public function render()
    {
        return view('livewire.home-page', [
            'featuredProducts' => Product::where('is_active', true)->where('is_featured', true)->latest()->take(6)->get(),
            'categories' => Category::where('is_active', true)->withCount('products')->take(6)->get(),
            'newArrivals' => Product::where('is_active', true)->latest()->take(4)->get(),
        ])->layout('layouts.app');
    }
}
