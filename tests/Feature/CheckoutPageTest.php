<?php

namespace Tests\Feature;

use App\Livewire\CheckoutPage;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_locked_cart_items_and_clears_only_checked_out_rows(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'checkout@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $product = Product::create([
            'name' => 'Amplifier',
            'slug' => 'amplifier',
            'price' => 300,
            'stock' => 3,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('customerName', 'Customer')
            ->set('customerEmail', 'checkout@example.test')
            ->set('customerPhone', '0123456789')
            ->set('street', '1 Jalan Test')
            ->set('city', 'Kuala Lumpur')
            ->set('postcode', '50000')
            ->set('state', 'Kuala Lumpur')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        // Order is created awaiting payment, then the user is sent to the payment page.
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_email' => 'checkout@example.test',
            'subtotal' => 600,
            'total_amount' => 600,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 300,
            'subtotal' => 600,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_out_of_stock_product_can_be_backordered(): void
    {
        $user = User::create([
            'name' => 'Backorder Buyer',
            'email' => 'backorder@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        // Zero on-hand stock — should still be orderable as a backorder.
        $product = Product::create([
            'name' => 'Backorder Subwoofer',
            'slug' => 'backorder-subwoofer',
            'price' => 500,
            'stock' => 0,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('customerName', 'Backorder Buyer')
            ->set('customerEmail', 'backorder@example.test')
            ->set('customerPhone', '0123456789')
            ->set('street', '1 Jalan Test')
            ->set('city', 'Shah Alam')
            ->set('postcode', '40150')
            ->set('state', 'Selangor')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 1000,
            'payment_status' => 'pending',
        ]);

        // Stock goes negative to represent the two units now owed.
        $this->assertSame(-2, $product->fresh()->stock);
    }
}
