<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPage extends Component
{
    use SetsSeo, WithPagination;

    // Livewire 4 syncs these to the query string via the #[Url] attribute — the
    // legacy `protected $queryString` property is ignored in v4, which is why the
    // search/filters never appeared in the URL. `except: ''` keeps empty values
    // out of the URL so a clean /products stays clean.
    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

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
            // In-stock products first, then genuine discounts. The portable CASE
            // expressions work on both TiDB/MySQL in production and SQLite tests.
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN 0 ELSE 1 END')
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
            $term = '%'.$escaped.'%';
            // The translated columns must be searchable too: a MS/ZH visitor sees
            // name_ms/name_zh on the cards, so searching by the very name they're
            // looking at has to match — not just the English source columns.
            $query->where(function ($q) use ($term) {
                foreach (['name', 'name_ms', 'name_zh', 'short_description', 'short_description_ms', 'short_description_zh', 'sku'] as $column) {
                    $q->orWhereRaw("{$column} LIKE ? ESCAPE '!'", [$term]);
                }
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
            'products' => $query->paginate(12),
            'allCategories' => $cats,
            'shoppingEnabled' => $shoppingEnabled,
        ])->layout('layouts.app');
    }
}
