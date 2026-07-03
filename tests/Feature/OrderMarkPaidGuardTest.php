<?php

namespace Tests\Feature;

use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression guard: the expiry scheduler cancels unpaid orders while LEAVING
 * payment_status 'pending', and markPaid's locked re-check only tested that
 * column — so a "Mark Paid" click racing a cancellation produced a
 * cancelled-but-paid order (stock already restocked) and emailed the customer
 * a confirmation. The re-check must mirror PaymentPage::pay()'s guard.
 */
class OrderMarkPaidGuardTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'subtotal'       => 100,
            'shipping_fee'   => 0,
            'total_amount'   => 100,
            'status'         => 'pending',
            'payment_status' => 'pending',
        ], $overrides));
    }

    public function test_a_cancelled_order_cannot_be_marked_paid(): void
    {
        $order = $this->order([
            'status'              => 'cancelled',
            'cancelled_by'        => 'system',
            'cancellation_reason' => 'Order expired',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($admin, 'admin');

        Livewire::test(ListOrders::class)
            ->assertTableActionHidden('markPaid', $order);

        // Belt and braces: even if the click slips past visibility (the race this
        // guards), the locked re-check must refuse the transition.
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertNotSame('paid', $order->fresh()->payment_status);
    }

    public function test_a_pending_order_can_still_be_marked_paid(): void
    {
        $order = $this->order();

        $admin = User::factory()->create(['role' => 'admin']);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($admin, 'admin');

        Livewire::test(ListOrders::class)
            ->callTableAction('markPaid', $order);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);
    }
}
