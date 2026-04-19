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
        return CartItem::where('session_id', session()->getId())
            ->with('product')
            ->get();
    }

    public function getSubtotalProperty(): float
    {
        return $this->cartItems->sum(fn($item) =>
            ($item->product->current_price ?? 0) * $item->quantity
        );
    }

    public function incrementQuantity(int $cartItemId): void
    {
        $item = CartItem::where('id', $cartItemId)
            ->where('session_id', session()->getId())
            ->first();

        if ($item && $item->quantity < ($item->product->stock ?? 99)) {
            $item->increment('quantity');
        }
    }

    public function decrementQuantity(int $cartItemId): void
    {
        $item = CartItem::where('id', $cartItemId)
            ->where('session_id', session()->getId())
            ->first();

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
        CartItem::where('id', $cartItemId)
            ->where('session_id', session()->getId())
            ->delete();
    }

    public function clearCart(): void
    {
        CartItem::where('session_id', session()->getId())->delete();
    }

    /**
     * Static helper: add a product to the session cart.
     */
    public static function addToCart(int $productId, int $quantity = 1): void
    {
        $sessionId = session()->getId();

        $existing = CartItem::where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity'   => $quantity,
            ]);
        }
    }

    public static function getCartCount(): int
    {
        return CartItem::where('session_id', session()->getId())->sum('quantity');
    }

    public function render()
    {
        return view('livewire.cart-page')->layout('layouts.app');
    }
}
