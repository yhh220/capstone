<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
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
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
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
        $this->dispatch('cart-updated', count: CartPage::getCartCount()); // badge + mini-cart sync
        $this->dispatch('cart-added');                                    // toast
    }

    public function render()
    {
        $shoppingEnabled = setting('ONLINE_SHOPPING_ENABLED') === 'true';

        // Price filtering only makes sense when prices are shown. When online
        // shopping is off, prices/stock/cart are hidden, so a price-range filter
        // would let customers filter by a number they can't see — ignore it.
        $min = $max = null;
        if ($shoppingEnabled) {
            $min = ($this->minPrice !== '' && is_numeric($this->minPrice)) ? (float) $this->minPrice : null;
            $max = ($this->maxPrice !== '' && is_numeric($this->maxPrice)) ? (float) $this->maxPrice : null;
            // Normalize price range — swap if user entered min > max
            if ($min !== null && $max !== null && $min > $max) {
                [$min, $max] = [$max, $min];
            }
        }

        $query = Product::where('is_active', true)
            ->with('category')
            ->latest();

        if ($this->search !== '') {
            // Escape the LIKE wildcards (% and _) so a term like "50% Tint Film"
            // matches literally. Use '!' as the ESCAPE character, NOT backslash:
            // in MySQL/TiDB a backslash is itself a string-literal escape, so
            // "ESCAPE '\\'" (which PHP renders as ESCAPE '\') breaks the string
            // literal and throws a 1064 syntax error (SQLite tolerated it, TiDB
            // does not). '!' has no special meaning in either engine.
            // Escape '!' first, then the wildcards, so added markers aren't doubled.
            $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $this->search);
            $term = '%' . $escaped . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw("name LIKE ? ESCAPE '!'", [$term])
                  ->orWhereRaw("short_description LIKE ? ESCAPE '!'", [$term])
                  ->orWhereRaw("sku LIKE ? ESCAPE '!'", [$term]);
            });
        }

        if ($this->category !== '') {
            $query->where('category_id', $this->category);
        }

        if ($min !== null) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$min]);
        }

        if ($max !== null) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$max]);
        }

        $cats = Category::where('is_active', true)->orderBy('name')->get();

        return view('livewire.products-page', [
            'products'        => $query->paginate(12),
            'allCategories'   => $cats,
            'shoppingEnabled' => $shoppingEnabled,
        ])->layout('layouts.app');
    }
}
