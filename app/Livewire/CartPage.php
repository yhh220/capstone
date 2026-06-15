<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    use SetsSeo;

    /** Compact single-column layout for the slide-over drawer (vs the full /cart page). */
    public bool $compact = false;

    /** Sane per-line cap — customers can backorder beyond on-hand stock. */
    public const MAX_QTY = 99;

    /** Re-render when the cart changes anywhere (mini-cart drawer stays in sync). */
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Computed cartItems/subtotal re-query on render; this just triggers it.
    }

    public function mount(): void
    {
        $this->setSeo(
            title: 'Shopping Cart',
            description: 'Review your cart items before checkout.',
        );
    }

    public function getCartItemsProperty()
    {
        return CartItem::forCurrentOwner()->with('product')->get();
    }

    public function getSubtotalProperty(): float
    {
        return $this->cartItems->sum(fn($item) =>
            ($item->product?->current_price ?? 0) * $item->quantity
        );
    }

    /**
     * Only a genuinely unavailable product (deleted or deactivated) blocks
     * checkout. Ordering more than on-hand stock is fine — it's a backorder.
     */
    public function getHasStockWarningsProperty(): bool
    {
        return $this->cartItems->contains(fn ($item): bool =>
            !$item->product || !$item->product->is_active
        );
    }

    public function incrementQuantity(int $cartItemId): void
    {
        $item = CartItem::forCurrentOwner()->where('id', $cartItemId)->first();

        if ($item && $item->quantity < self::MAX_QTY) {
            $item->increment('quantity');
            $this->dispatch('cart-updated', count: self::getCartCount());
        }
    }

    public function decrementQuantity(int $cartItemId): void
    {
        $item = CartItem::forCurrentOwner()->where('id', $cartItemId)->first();

        if ($item) {
            if ($item->quantity <= 1) {
                $item->delete();
            } else {
                $item->decrement('quantity');
            }
            $this->dispatch('cart-updated', count: self::getCartCount());
        }
    }

    public function removeItem(int $cartItemId): void
    {
        CartItem::forCurrentOwner()->where('id', $cartItemId)->delete();
        $this->dispatch('cart-updated');
    }

    public function clearCart(): void
    {
        CartItem::forCurrentOwner()->delete();
        $this->dispatch('cart-updated');
    }

    /**
     * Static helper: add an active product to the current owner's cart. Not
     * limited by stock — out-of-stock items can be backordered — only capped
     * at a sane per-line maximum.
     */
    public static function addToCart(int $productId, int $quantity = 1): void
    {
        $product = Product::where('is_active', true)->find($productId);
        if (!$product) {
            return;
        }

        $existing = CartItem::forCurrentOwner()
            ->where('product_id', $productId)
            ->first();

        $currentQty = $existing?->quantity ?? 0;
        $targetQty  = min($currentQty + $quantity, self::MAX_QTY);

        if ($targetQty <= $currentQty) {
            return; // already at the per-line cap
        }

        if ($existing) {
            $existing->update(['quantity' => $targetQty]);
        } else {
            CartItem::create(array_merge(
                CartItem::currentOwnerAttributes(),
                ['product_id' => $productId, 'quantity' => $targetQty],
            ));
        }
    }

    public static function getCartCount(): int
    {
        return (int) CartItem::forCurrentOwner()->sum('quantity');
    }

    public function render()
    {
        return view('livewire.cart-page')->layout('layouts.app');
    }
}
