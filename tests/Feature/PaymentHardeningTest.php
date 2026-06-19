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

class PaymentHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => 'Tan',
            'customer_email' => 't@example.test',
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1', 'city' => 'KL', 'postcode' => '40150', 'state' => 'Sel'],
            'subtotal'       => 300, 'shipping_fee' => 0, 'total_amount' => 300,
            'status'         => 'processing', 'payment_status' => 'pending', 'payment_method' => 'FPX',
        ], $overrides));
    }

    public function test_paying_stamps_paid_at(): void
    {
        $user = User::create(['name' => 'B', 'email' => 'b@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp', 'price' => 300, 'stock' => 5, 'is_active' => true]);
        $order = $this->order(['user_id' => $user->id, 'status' => 'pending', 'expires_at' => now()->addMinutes(15)]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => 'Amp', 'quantity' => 1, 'unit_price' => 300, 'subtotal' => 300]);

        Livewire::actingAs($user)->test(PaymentPage::class, ['orderNumber' => $order->order_number])->call('pay');

        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_cancelling_restocks_and_stamps_cancelled_at(): void
    {
        $product = Product::create(['name' => 'Sub', 'slug' => 'sub', 'price' => 300, 'stock' => 1, 'is_active' => true]); // 1 left after reserving 2
        $order = $this->order();
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => 'Sub', 'quantity' => 2, 'unit_price' => 300, 'subtotal' => 600]);

        // The admin "Cancel & restock" core: return stock + mark cancelled.
        $order->load('items');
        $order->restockItems();
        $order->update(['status' => 'cancelled']);

        $this->assertSame(3, $product->fresh()->stock);     // 1 + 2 returned
        $this->assertNotNull($order->fresh()->cancelled_at); // auto-stamped by the event
    }

    public function test_marking_shipped_stamps_shipped_at(): void
    {
        $order = $this->order();
        $order->update(['status' => 'shipped']);
        $this->assertNotNull($order->fresh()->shipped_at);
    }

    public function test_order_number_never_collides_with_a_soft_deleted_order(): void
    {
        $first = $this->order();
        $first->delete(); // soft-deleted — still owns its order_number in the unique index

        $next = Order::generateOrderNumber();

        $this->assertNotSame($first->order_number, $next);
        $this->assertFalse(Order::withTrashed()->where('order_number', $next)->exists());
    }
}
