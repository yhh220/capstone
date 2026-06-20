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

    public function test_prune_only_removes_old_abandoned_guest_carts(): void
    {
        $product = Product::create([
            'name' => 'Amp', 'slug' => 'amp-prune-test', 'price' => 100, 'stock' => 5, 'is_active' => true,
        ]);

        $oldGuest = CartItem::create(['session_id' => 'old-guest', 'product_id' => $product->id, 'quantity' => 1]);
        $oldGuest->forceFill(['updated_at' => now()->subDays(31)])->saveQuietly();

        $recentGuest = CartItem::create(['session_id' => 'recent-guest', 'product_id' => $product->id, 'quantity' => 1]);

        $user = User::create(['name' => 'Loyal', 'email' => 'loyal@example.test', 'password' => 'password', 'role' => 'client']);
        $oldUserCart = CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);
        $oldUserCart->forceFill(['updated_at' => now()->subDays(31)])->saveQuietly();

        $oldGuest->prunable()->get()->each(fn ($item) => $item->delete());

        $this->assertDatabaseMissing('cart_items', ['id' => $oldGuest->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $recentGuest->id]);
        // A logged-in customer's cart is never auto-pruned, no matter how old.
        $this->assertDatabaseHas('cart_items', ['id' => $oldUserCart->id]);
    }
}
