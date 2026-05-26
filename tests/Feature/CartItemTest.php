<?php

namespace Tests\Feature;

use App\Livewire\CartPage;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_item_without_session_id(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $product = Product::create([
            'name' => 'Speaker Kit',
            'slug' => 'speaker-kit',
            'price' => 250,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        CartPage::addToCart($product->id, 2);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'session_id' => null,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_guest_cart_claim_can_clear_session_id(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'claim@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $product = Product::create([
            'name' => 'Tint Film',
            'slug' => 'tint-film',
            'price' => 180,
            'stock' => 5,
            'is_active' => true,
        ]);

        $guestItem = CartItem::create([
            'session_id' => 'guest-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        CartItem::claimGuestCart('guest-session', $user->id);

        $this->assertDatabaseHas('cart_items', [
            'id' => $guestItem->id,
            'user_id' => $user->id,
            'session_id' => null,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
}
