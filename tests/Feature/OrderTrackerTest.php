<?php

namespace Tests\Feature;

use App\Livewire\OrderTracker;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_number_with_surrounding_whitespace_still_matches(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-2026-00042',
            'customer_name' => 'Test',
            'customer_email' => 'tracker@example.test',
            'customer_phone' => '0123456789',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total_amount' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        Livewire::test(OrderTracker::class)
            ->set('orderNumber', '  ORD-2026-00042  ')
            ->set('email', 'tracker@example.test')
            ->call('trackOrder')
            ->assertSet('order.id', $order->id)
            ->assertSet('errorMsg', '');
    }
}
