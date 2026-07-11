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

    public function test_out_of_stock_product_is_backordered_at_checkout(): void
    {
        $user = User::create([
            'name' => 'Backorder Buyer',
            'email' => 'backorder@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        // Zero on-hand stock — checkout still succeeds. The cart and product
        // pages both advertise backordering (BACKORDER_DAYS setting, "ships in
        // ~N days" badge), so checkout must honour that instead of rejecting it.
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
            'customer_email' => 'backorder@example.test',
        ]);

        // Stock goes negative — that deficit is exactly what's owed to the
        // customer, and nets back to a normal count on the next restock.
        $this->assertSame(-2, $product->fresh()->stock);
    }

    public function test_sufficient_stock_allows_checkout(): void
    {
        $user = User::create([
            'name' => 'In-Stock Buyer',
            'email' => 'instock@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $product = Product::create([
            'name' => 'In-Stock Subwoofer',
            'slug' => 'in-stock-subwoofer',
            'price' => 500,
            'stock' => 5,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('customerName', 'In-Stock Buyer')
            ->set('customerEmail', 'instock@example.test')
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

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_unavailable_product_shows_a_visible_error_instead_of_failing_silently(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'silent-fail@example.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $product = Product::create([
            'name' => 'Discontinued Amp',
            'slug' => 'discontinued-amp',
            'price' => 300,
            'stock' => 3,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Deactivated after being added to the cart but before checkout.
        $product->update(['is_active' => false]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('customerName', 'Customer')
            ->set('customerEmail', 'silent-fail@example.test')
            ->set('customerPhone', '0123456789')
            ->set('street', '1 Jalan Test')
            ->set('city', 'Kuala Lumpur')
            ->set('postcode', '50000')
            ->set('state', 'Kuala Lumpur')
            ->call('placeOrder')
            ->assertHasErrors('stock')
            ->assertSee('A product in your cart is no longer available.');

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    public function test_store_pickup_needs_no_address_and_charges_no_shipping(): void
    {
        $user = User::create(['name' => 'Collector', 'email' => 'pickup@example.test', 'password' => 'password', 'role' => 'client']);
        // Priced below the free-shipping threshold, so delivery WOULD have
        // charged the flat rate — proving the zero fee comes from pickup.
        $product = Product::create(['name' => 'Dashcam', 'slug' => 'dashcam', 'price' => 100, 'stock' => 3, 'is_active' => true]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('deliveryMethod', 'pickup')
            ->set('customerName', 'Collector')
            ->set('customerEmail', 'pickup@example.test')
            ->set('customerPhone', '0123456789')
            // No street/city/postcode/state on purpose.
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'delivery_method' => 'pickup',
            'shipping_fee' => 0,
            'total_amount' => 100,
            'shipping_address' => null,
        ]);
    }

    public function test_courier_delivery_still_requires_the_full_address(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'courier@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Horn', 'slug' => 'horn', 'price' => 50, 'stock' => 3, 'is_active' => true]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('deliveryMethod', 'delivery')
            ->set('customerName', 'Buyer')
            ->set('customerEmail', 'courier@example.test')
            ->set('customerPhone', '0123456789')
            ->set('street', '')
            ->call('placeOrder')
            ->assertHasErrors(['street']);

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    public function test_profile_address_prefills_checkout_for_a_first_time_buyer(): void
    {
        // No previous orders: the address saved on the profile must seed the
        // form (it used to be silently ignored — only a past order's address
        // was ever prefilled).
        $user = User::create([
            'name' => 'Fresh Buyer', 'email' => 'fresh@example.test', 'password' => 'password', 'role' => 'client',
            'phone' => '0129998888', 'address_line' => '88 Jalan Profile', 'city' => 'Shah Alam',
            'postcode' => '40150', 'state' => 'Selangor',
        ]);
        $product = Product::create(['name' => 'Tint', 'slug' => 'tint', 'price' => 80, 'stock' => 3, 'is_active' => true]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->assertSet('street', '88 Jalan Profile')
            ->assertSet('city', 'Shah Alam')
            ->assertSet('postcode', '40150')
            ->assertSet('state', 'Selangor')
            ->assertSet('customerPhone', '0129998888');
    }

    public function test_a_forged_delivery_method_falls_back_to_delivery(): void
    {
        $user = User::create(['name' => 'Forger', 'email' => 'forge@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Cable', 'slug' => 'cable', 'price' => 20, 'stock' => 3, 'is_active' => true]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        // 'drone_drop' is not a real method: the whitelist must coerce it back
        // to 'delivery', whose address rules then reject the empty address —
        // a crafted request can't invent a fee-free fulfilment mode.
        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->set('deliveryMethod', 'drone_drop')
            ->set('customerName', 'Forger')
            ->set('customerEmail', 'forge@example.test')
            ->set('customerPhone', '0123456789')
            ->call('placeOrder')
            ->assertHasErrors(['street']);

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }
}
