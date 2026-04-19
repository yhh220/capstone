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

    protected $queryString = [
        'category' => ['except' => ''],
        'search'   => ['except' => ''],
    ];

    public function mount(): void
    {
        $category = request()->query('category', '');
        if ($category !== '' && is_numeric($category)) {
            $this->category = (string) $category;
        }

        $this->setSeo(
            title: 'Our Products',
            description: 'Browse our full range of car audio, window tint, and accessories. Filter by category and enquire on WhatsApp.',
        );
    }

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedMinPrice(): void { $this->resetPage(); }
    public function updatedMaxPrice(): void { $this->resetPage(); }

    public function addToCart(int $productId): void
    {
        CartPage::addToCart($productId);
        session()->flash('success', __('Added to cart!'));
    }

    public function render()
    {
        // Normalize price range — swap if user entered min > max
        $min = ($this->minPrice !== '' && is_numeric($this->minPrice)) ? (float) $this->minPrice : null;
        $max = ($this->maxPrice !== '' && is_numeric($this->maxPrice)) ? (float) $this->maxPrice : null;
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        $query = Product::where('is_active', true)
            ->with('category')
            ->latest();

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('short_description', 'like', $term)
                  ->orWhere('sku', 'like', $term);
            });
        }

        if ($this->category !== '') {
            $query->where('category_id', $this->category);
        }

        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        return view('livewire.products-page', [
            'products'        => $query->paginate(12),
            'categories'      => Category::where('is_active', true)->orderBy('name')->get(),
            'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
        ])->layout('layouts.app');
    }
}
