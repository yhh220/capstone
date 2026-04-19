<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;

class CartPage extends Component
{
    use SetsSeo;

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

    public function incrementQuantity(int $cartItemId): void
    {
        $item = CartItem::forCurrentOwner()->where('id', $cartItemId)->first();

        if ($item && $item->quantity < ($item->product?->stock ?? 99)) {
            $item->increment('quantity');
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
        }
    }

    public function removeItem(int $cartItemId): void
    {
        CartItem::forCurrentOwner()->where('id', $cartItemId)->delete();
    }

    public function clearCart(): void
    {
        CartItem::forCurrentOwner()->delete();
    }

    /**
     * Static helper: add a product to the current owner's cart, clamped to stock.
     */
    public static function addToCart(int $productId, int $quantity = 1): void
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $maxStock = $product->stock ?? 99;
        if ($maxStock <= 0) {
            return;
        }

        $existing = CartItem::forCurrentOwner()
            ->where('product_id', $productId)
            ->first();

        $currentQty = $existing?->quantity ?? 0;
        $targetQty  = min($currentQty + $quantity, $maxStock);

        if ($targetQty <= $currentQty) {
            return; // already at or above stock ceiling
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
