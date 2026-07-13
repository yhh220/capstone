<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ProductDetail extends Component
{
    use SetsSeo, WithPagination;

    public Product $product;
    public int $quantity = 1;

    public int $reviewRating = 5;

    public string $reviewComment = '';

    /** newest | highest | lowest */
    public string $reviewSort = 'newest';

    public function updatedReviewSort(): void
    {
        if (! in_array($this->reviewSort, ['newest', 'highest', 'lowest'], true)) {
            $this->reviewSort = 'newest';
        }

        $this->resetPage('reviewsPage');
    }

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
        // Not stock-limited — out-of-stock items can be backordered.
        if ($this->quantity < CartPage::MAX_QTY) {
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
        // No stock gate — out-of-stock items can be backordered.
        CartPage::addToCart($this->product->id, $this->quantity);
        $this->dispatch('cart-updated', count: CartPage::getCartCount()); // badge + mini-cart sync
        $this->dispatch('cart-added');                                    // toast
        $this->quantity = 1;
    }

    public function submitReview(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if (! $this->hasCompletedPurchase()) {
            $this->addError('reviewComment', __('Only customers with a completed order can review this product.'));
            return;
        }

        $this->validate([
            'reviewRating' => 'required|integer|between:1,5',
            'reviewComment' => 'required|string|min:10|max:1000',
        ]);

        $key = 'product-review:'.Auth::id().':'.$this->product->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('reviewComment', __('Please wait a moment before trying again.'));
            return;
        }

        $review = ProductReview::firstOrNew([
            'product_id' => $this->product->id,
            'user_id' => Auth::id(),
        ]);
        $review->rating = $this->reviewRating;
        $review->comment = trim($this->reviewComment);

        // New verified-purchase reviews are public immediately. If staff have
        // deliberately hidden an existing review, preserve that decision.
        if (! $review->exists) {
            $review->is_approved = true;
        }
        $review->save();
        RateLimiter::hit($key, 3600);
        $this->reset('reviewComment');
        session()->flash('review-success', __('Thank you. Your review has been saved.'));
    }

    private function hasCompletedPurchase(): bool
    {
        return Auth::check() && OrderItem::query()
            ->where('product_id', $this->product->id)
            ->whereHas('order', fn ($query) => $query->where('user_id', Auth::id())->where('status', 'delivered'))
            ->exists();
    }

    public function render()
    {
        $reviewsQuery = $this->product->visibleReviews()->with('user:id,name');

        match ($this->reviewSort) {
            'highest' => $reviewsQuery->orderByDesc('rating')->orderByDesc('created_at'),
            'lowest' => $reviewsQuery->orderBy('rating')->orderByDesc('created_at'),
            default => $reviewsQuery->latest(),
        };

        $reviews = $reviewsQuery->paginate(5, ['*'], 'reviewsPage');
        $reviewSummary = $this->product->visibleReviews()
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();
        $myReview = Auth::check()
            ? ProductReview::where('product_id', $this->product->id)->where('user_id', Auth::id())->first()
            : null;

        if ($myReview && $this->reviewComment === '') {
            $this->reviewRating = $myReview->rating;
            $this->reviewComment = $myReview->comment;
        }

        return view('livewire.product-detail', [
            'related' => Product::where('category_id', $this->product->category_id)
                ->where('id', '!=', $this->product->id)
                ->where('is_active', true)
                ->take(4)
                ->get(),
            'translatedDescription' => $this->product->translated_description,
            'shoppingEnabled' => setting('ONLINE_SHOPPING_ENABLED') === 'true',
            'galleryMedia' => $this->product->getMedia('images'),
            'reviews' => $reviews,
            'reviewAverage' => $reviewSummary->average,
            'reviewCount' => (int) $reviewSummary->total,
            'canReview' => $this->hasCompletedPurchase(),
            'myReview' => $myReview,
        ])->layout('layouts.app');
    }
}
