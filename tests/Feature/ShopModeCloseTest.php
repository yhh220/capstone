<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\ShopModeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Turning online shopping OFF must "clear the board": every unpaid order is
 * cancelled and its stock released (so no customer is left with a pending order
 * they can no longer pay for), while PAID orders are left completely untouched
 * (real money owed, fulfilled as usual). The announcement bar surfaces the
 * SITE_ANNOUNCEMENT_TEXT when enabled.
 */
class ShopModeCloseTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5): Product
    {
        return Product::create([
            'name' => 'Speaker Kit', 'slug' => 'speaker-kit', 'price' => 250,
            'stock' => $stock, 'is_active' => true,
        ]);
    }

    private function order(string $paymentStatus, Product $product, int $qty = 2): Order
    {
        $order = Order::create([
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'subtotal'       => 500,
            'shipping_fee'   => 0,
            'total_amount'   => 500,
            'status'         => $paymentStatus === 'paid' ? 'processing' : 'pending',
            'payment_status' => $paymentStatus,
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name,
            'quantity' => $qty, 'unit_price' => 250, 'subtotal' => 500,
        ]);

        return $order;
    }

    public function test_closing_shop_cancels_unpaid_orders_and_restocks(): void
    {
        Mail::fake();
        $product = $this->product(stock: 5);
        $unpaid  = $this->order('pending', $product, qty: 2);

        $cancelled = app(ShopModeService::class)->cancelUnpaidOrders();

        $this->assertSame(1, $cancelled);
        $this->assertSame('cancelled', $unpaid->fresh()->status);
        $this->assertSame('system', $unpaid->fresh()->cancelled_by);
        $this->assertSame(7, $product->fresh()->stock, 'The 2 reserved units must be returned to stock.');
    }

    public function test_closing_shop_leaves_paid_orders_untouched(): void
    {
        Mail::fake();
        $product = $this->product(stock: 5);
        $paid    = $this->order('paid', $product, qty: 2);

        app(ShopModeService::class)->cancelUnpaidOrders();

        $this->assertSame('processing', $paid->fresh()->status, 'A paid order must never be cancelled by closing the shop.');
        $this->assertNull($paid->fresh()->cancelled_at);
        $this->assertSame(5, $product->fresh()->stock, 'Paid orders must not restock.');
    }

    public function test_toggling_the_setting_off_triggers_the_cleanup(): void
    {
        Mail::fake();
        Setting::setValue('ONLINE_SHOPPING_ENABLED', 'true');
        $product = $this->product(stock: 5);
        $unpaid  = $this->order('pending', $product, qty: 1);

        // The model's updated event should fire the cleanup on a true→false change.
        Setting::setValue('ONLINE_SHOPPING_ENABLED', 'false');

        $this->assertSame('cancelled', $unpaid->fresh()->status);
        $this->assertSame(6, $product->fresh()->stock);
    }

    public function test_turning_the_setting_on_does_not_cancel_anything(): void
    {
        Mail::fake();
        Setting::setValue('ONLINE_SHOPPING_ENABLED', 'false');
        $product = $this->product(stock: 5);
        $unpaid  = $this->order('pending', $product, qty: 1);

        Setting::setValue('ONLINE_SHOPPING_ENABLED', 'true');

        $this->assertSame('pending', $unpaid->fresh()->status, 'Re-opening the shop must not cancel orders.');
    }

    public function test_announcement_bar_shows_when_enabled(): void
    {
        Setting::setValue('SITE_ANNOUNCEMENT_ENABLED', 'true');
        Setting::setValue('SITE_ANNOUNCEMENT_TEXT', 'We are under maintenance right now.');

        $this->get(route('products'))
            ->assertOk()
            ->assertSee('We are under maintenance right now.');
    }

    public function test_announcement_bar_hidden_when_disabled(): void
    {
        Setting::setValue('SITE_ANNOUNCEMENT_ENABLED', 'false');
        Setting::setValue('SITE_ANNOUNCEMENT_TEXT', 'Hidden maintenance message.');

        $this->get(route('products'))
            ->assertOk()
            ->assertDontSee('Hidden maintenance message.');
    }
}
