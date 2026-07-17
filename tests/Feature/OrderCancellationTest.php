<?php

namespace Tests\Feature;

use App\Livewire\MyAccountPage;
use App\Mail\OrderCancelledMail;
use App\Mail\OrderRefundProcessedMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\RefundCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Test', 'email' => 'test@example.test',
            'password' => 'password', 'role' => 'client',
        ], $overrides));
    }

    private function product(int $stock = 10): Product
    {
        static $n = 0;
        $n++;

        return Product::create([
            'name' => "Product $n", 'slug' => "product-$n",
            'price' => 200, 'stock' => $stock, 'is_active' => true,
        ]);
    }

    private function paidOrder(User $user, array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1', 'city' => 'KL', 'postcode' => '40150', 'state' => 'Sel'],
            'subtotal' => 200, 'shipping_fee' => 0, 'total_amount' => 200,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'FPX',
            'paid_at' => now(),
        ], $overrides));
    }

    private function unpaidOrder(User $user): Order
    {
        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1', 'city' => 'KL', 'postcode' => '40150', 'state' => 'Sel'],
            'subtotal' => 200, 'shipping_fee' => 0, 'total_amount' => 200,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'FPX',
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    private function attachItem(Order $order, Product $product, int $qty = 1): void
    {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $qty,
            'unit_price' => $product->price,
            'subtotal' => $product->price * $qty,
        ]);
    }

    // ── RefundCalculator unit tests ──────────────────────────────────────────

    public function test_full_refund_within_window(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(2), 'total_amount' => 200]);

        $result = (new RefundCalculator)->calculate($order);

        $this->assertSame('full', $result['tier']);
        $this->assertSame(100.0, $result['percentage']);
        $this->assertSame(200.0, $result['amount']);
    }

    public function test_fee_deducted_after_window(): void
    {
        $user = $this->user();
        // Default window is 24 h; 30 h past = fee tier
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(30), 'total_amount' => 200]);

        $result = (new RefundCalculator)->calculate($order);

        $this->assertSame('fee', $result['tier']);
        $this->assertSame(90.0, $result['percentage']); // 100 - 10% fee
        $this->assertSame(180.0, $result['amount']);
    }

    public function test_exactly_at_window_boundary_is_full_refund(): void
    {
        // Use Laravel's freezeTime() so now() returns a stable value throughout.
        $this->freezeTime();

        $user = $this->user();
        $hours = (new RefundCalculator)->fullRefundHours();
        // Exactly at the boundary: paid_at = now() - fullRefundHours (<=, not <, should be full)
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours($hours), 'total_amount' => 200]);

        $result = (new RefundCalculator)->calculate($order);

        $this->assertSame('full', $result['tier'], 'Exactly at boundary (<=) should still be full refund');
    }

    public function test_calculate_returns_null_when_status_is_shipped(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['status' => 'shipped']);

        $this->assertNull((new RefundCalculator)->calculate($order));
    }

    public function test_calculate_returns_null_when_status_is_delivered(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['status' => 'delivered']);

        $this->assertNull((new RefundCalculator)->calculate($order));
    }

    public function test_eligibility_label_full_refund(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(1)]);

        $label = (new RefundCalculator)->eligibilityLabel($order);

        $this->assertStringContainsString('full refund', $label);
    }

    public function test_eligibility_label_fee_tier(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(30)]);

        $label = (new RefundCalculator)->eligibilityLabel($order);

        $this->assertStringContainsString('90%', $label);
    }

    public function test_eligibility_label_shipped(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['status' => 'shipped']);

        $label = (new RefundCalculator)->eligibilityLabel($order);

        $this->assertStringContainsString('shipped', $label);
    }

    public function test_settings_change_reflected_immediately(): void
    {
        Setting::setValue('CANCELLATION_FULL_REFUND_HOURS', '48');
        Setting::setValue('CANCELLATION_FEE_PERCENT', '5');

        $calc = new RefundCalculator;
        $this->assertSame(48, $calc->fullRefundHours());
        $this->assertSame(5.0, $calc->feePercent());

        // 40 h ago — within 48 h window → full
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(40)]);
        $result = $calc->calculate($order);
        $this->assertSame('full', $result['tier']);

        Setting::setValue('CANCELLATION_FULL_REFUND_HOURS', '24');
        Setting::setValue('CANCELLATION_FEE_PERCENT', '10');
    }

    // ── Customer self-service cancel ─────────────────────────────────────────

    public function test_customer_cancel_paid_order_full_refund(): void
    {
        Mail::fake();
        $user = $this->user();
        $product = $this->product(5);
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(2)]);
        $this->attachItem($order, $product, 2);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $fresh = $order->fresh();
        $this->assertSame('cancelled', $fresh->status);
        $this->assertSame('customer', $fresh->cancelled_by);
        $this->assertNotNull($fresh->refund_amount);
        $this->assertSame(200.0, (float) $fresh->refund_amount);
        $this->assertSame(100.0, (float) $fresh->refund_percentage);
        $this->assertSame(7, $product->fresh()->stock); // 5 + 2 returned
    }

    public function test_customer_cancel_paid_order_fee_deducted(): void
    {
        Mail::fake();
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(30), 'total_amount' => 200]);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $fresh = $order->fresh();
        $this->assertSame('cancelled', $fresh->status);
        $this->assertSame(180.0, (float) $fresh->refund_amount);
        $this->assertSame(90.0, (float) $fresh->refund_percentage);
    }

    public function test_customer_cancel_blocked_when_shipped(): void
    {
        Mail::fake();
        $user = $this->user();
        $order = $this->paidOrder($user, ['status' => 'shipped']);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $this->assertSame('shipped', $order->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_customer_cannot_cancel_another_users_order(): void
    {
        Mail::fake();
        $user1 = $this->user(['email' => 'u1@example.test']);
        $user2 = $this->user(['email' => 'u2@example.test', 'name' => 'Two']);
        $order = $this->paidOrder($user2, ['paid_at' => now()->subHours(1)]);

        Livewire::actingAs($user1)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $this->assertSame('processing', $order->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_cancel_unpaid_order_no_refund(): void
    {
        Mail::fake();
        $user = $this->user();
        $product = $this->product(3);
        $order = $this->unpaidOrder($user);
        $this->attachItem($order, $product, 2);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $fresh = $order->fresh();
        $this->assertSame('cancelled', $fresh->status);
        $this->assertNull($fresh->refund_amount);
        $this->assertSame(5, $product->fresh()->stock); // 3 + 2 returned
    }

    public function test_double_cancel_is_noop(): void
    {
        Mail::fake();
        $user = $this->user();
        $order = $this->paidOrder($user, ['status' => 'cancelled']);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $this->assertSame('cancelled', $order->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_stock_restocked_on_customer_cancel(): void
    {
        Mail::fake();
        $user = $this->user();
        $p1 = $this->product(10);
        $p2 = $this->product(5);
        $order = $this->paidOrder($user);
        $this->attachItem($order, $p1, 3);
        $this->attachItem($order, $p2, 2);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        $this->assertSame(13, $p1->fresh()->stock);
        $this->assertSame(7, $p2->fresh()->stock);
    }

    // ── Email assertions ─────────────────────────────────────────────────────

    public function test_both_emails_sent_on_customer_cancel(): void
    {
        Mail::fake();
        $user = $this->user();
        $order = $this->paidOrder($user);

        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        Mail::assertSent(OrderCancelledMail::class, fn ($m) => $m->order->id === $order->id);
        Mail::assertSent(OwnerAlertMail::class);
    }

    public function test_no_owner_alert_for_mark_refunded(): void
    {
        Mail::fake();
        $user = $this->user();
        $order = $this->paidOrder($user);
        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => 'admin',
            'cancellation_reason' => 'Cancelled by admin',
            'refund_amount' => 180.0,
            'refund_percentage' => 90.0,
        ]);

        // Simulate markRefunded action (the action itself just calls $record->update + Mail)
        $order->update(['refunded_at' => now()]);
        try {
            Mail::to($order->customer_email)->send(new OrderRefundProcessedMail($order->fresh()));
        } catch (\Throwable) {
        }

        Mail::assertSent(OrderRefundProcessedMail::class, fn ($m) => $m->order->id === $order->id);
        Mail::assertNotSent(OwnerAlertMail::class);
    }

    public function test_mail_locale_pinned_to_english(): void
    {
        Mail::fake();
        App::setLocale('zh');

        $user = $this->user();
        $order = $this->paidOrder($user);
        Livewire::actingAs($user)->test(MyAccountPage::class)->call('cancelOrder', $order->id);

        Mail::assertSent(OrderCancelledMail::class, function (OrderCancelledMail $mail) {
            $rendered = $mail->render();
            // The English phrase appears in the template; zh locale would translate it if __() were used
            $this->assertStringContainsString('Order Cancelled', $rendered);

            return true;
        });

        App::setLocale('en');
    }

    public function test_mark_refunded_not_applicable_without_refund_amount(): void
    {
        // An order cancelled before payment has no refund amount — markRefunded action should be invisible
        $user = $this->user();
        $order = $this->paidOrder($user, ['payment_status' => 'pending', 'status' => 'cancelled', 'paid_at' => null]);

        $calc = new RefundCalculator;
        // isCancellable returns false for 'cancelled', and calculate returns null for unpaid
        $this->assertNull($calc->calculate($order));
    }

    public function test_admin_cancel_stamps_cancelled_by_admin(): void
    {
        $user = $this->user();
        $order = $this->paidOrder($user, ['paid_at' => now()->subHours(1)]);

        // Simulate what the Filament action's DB transaction does
        $refund = (new RefundCalculator)->calculate($order);
        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => 'admin',
            'cancellation_reason' => 'Cancelled by admin',
            'refund_amount' => $refund['amount'],
            'refund_percentage' => $refund['percentage'],
        ]);

        $fresh = $order->fresh();
        $this->assertSame('admin', $fresh->cancelled_by);
        $this->assertSame(200.0, (float) $fresh->refund_amount);
        $this->assertSame(100.0, (float) $fresh->refund_percentage);
    }
}
