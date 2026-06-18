<?php

namespace Tests\Feature;

use App\Livewire\PaymentPage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(User $user, Product $product, \DateTimeInterface $expiresAt): Order
    {
        $order = Order::create([
            'user_id'        => $user->id,
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1 Jln', 'city' => 'KL', 'postcode' => '50000', 'state' => 'KL'],
            'subtotal'       => 600,
            'shipping_fee'   => 0,
            'total_amount'   => 600,
            'status'         => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'FPX - Maybank2u',
            'expires_at'     => $expiresAt,
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'quantity'     => 2,
            'unit_price'   => 300,
            'subtotal'     => 600,
        ]);

        return $order;
    }

    public function test_paying_marks_the_order_paid_and_clears_the_timer(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp', 'price' => 300, 'stock' => 1, 'is_active' => true]);
        $order = $this->makeOrder($user, $product, now()->addMinutes(15));

        Livewire::actingAs($user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay')
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertNull($order->expires_at);
    }

    public function test_expired_unpaid_order_is_cancelled_and_stock_released(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer2@example.test', 'password' => 'password', 'role' => 'client']);
        // Stock is 1 — reflecting that checkout already reserved 2 from an original 3.
        $product = Product::create(['name' => 'Sub', 'slug' => 'sub', 'price' => 300, 'stock' => 1, 'is_active' => true]);
        $this->makeOrder($user, $product, now()->subMinute()); // already past the window

        $this->artisan('orders:expire-unpaid')->assertSuccessful();

        $order = Order::where('user_id', $user->id)->first();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(3, $product->fresh()->stock); // 1 + 2 released back
    }

    public function test_cannot_pay_an_expired_order(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer3@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Tweeter', 'slug' => 'tw', 'price' => 300, 'stock' => 1, 'is_active' => true]);
        $order = $this->makeOrder($user, $product, now()->subMinute());

        // mount() should expire it; pay() must not revive it.
        Livewire::actingAs($user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay');

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(3, $product->fresh()->stock);
    }
}
