<?php

namespace Tests\Feature;

use App\Livewire\PaymentPage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression guard: expireOrder() is a public Livewire action, and it used to
 * check only "awaiting payment" — a customer could invoke it from the browser
 * console BEFORE the 15-minute window lapsed and stamp their own cancellation
 * as cancelled_by='system' ("payment not completed"), polluting the audit
 * trail. It must refuse until expires_at has actually passed.
 */
class PaymentExpiryGuardTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $user, \DateTimeInterface $expiresAt): Order
    {
        return Order::create([
            'user_id'        => $user->id,
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'subtotal'       => 100,
            'shipping_fee'   => 0,
            'total_amount'   => 100,
            'status'         => 'pending',
            'payment_status' => 'pending',
            'expires_at'     => $expiresAt,
        ]);
    }

    public function test_expire_order_refuses_before_the_payment_window_lapses(): void
    {
        $user  = User::factory()->create(['role' => 'client']);
        $order = $this->order($user, now()->addMinutes(10)); // still inside the window

        $this->actingAs($user);

        Livewire::test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('expireOrder');

        $fresh = $order->fresh();
        $this->assertSame('pending', $fresh->status, 'An unexpired order must not be cancellable via expireOrder().');
        $this->assertNull($fresh->cancelled_at);
    }

    public function test_expire_order_cancels_once_the_window_has_lapsed(): void
    {
        $user  = User::factory()->create(['role' => 'client']);
        $order = $this->order($user, now()->subMinute()); // window already over

        $this->actingAs($user);

        Livewire::test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('expireOrder');

        $fresh = $order->fresh();
        $this->assertSame('cancelled', $fresh->status);
        $this->assertSame('system', $fresh->cancelled_by);
    }
}
