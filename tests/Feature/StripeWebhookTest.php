<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'whsec_test';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.webhook_secret' => self::WEBHOOK_SECRET]);
    }

    private function makeOrder(?\DateTimeInterface $expiresAt = null): Order
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp', 'price' => 300, 'stock' => 1, 'is_active' => true]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1 Jln', 'city' => 'KL', 'postcode' => '50000', 'state' => 'KL'],
            'subtotal' => 600,
            'shipping_fee' => 0,
            'total_amount' => 600,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'FPX - Maybank2u',
            'stripe_session_id' => 'cs_test_1',
            'expires_at' => $expiresAt ?? now()->addMinutes(30),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 300,
            'subtotal' => 600,
        ]);

        return $order;
    }

    private function sessionPayload(Order $order, string $type = 'checkout.session.completed', array $overrides = []): string
    {
        $session = array_merge([
            'id' => 'cs_test_1',
            'object' => 'checkout.session',
            'payment_status' => 'paid',
            'payment_intent' => 'pi_test_1',
            'client_reference_id' => $order->order_number,
            'metadata' => ['order_id' => (string) $order->id, 'order_number' => $order->order_number],
        ], $overrides);

        return json_encode([
            'id' => 'evt_test_1',
            'object' => 'event',
            'type' => $type,
            'api_version' => '2026-06-01',
            'data' => ['object' => $session],
        ]);
    }

    private function postWebhook(string $payload, ?string $signature = null): TestResponse
    {
        if ($signature === null) {
            $t = time();
            $signature = "t={$t},v1=".hash_hmac('sha256', $t.'.'.$payload, self::WEBHOOK_SECRET);
        }

        return $this->call('POST', '/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }

    public function test_rejects_a_bad_signature_and_leaves_the_order_untouched(): void
    {
        Mail::fake();
        $order = $this->makeOrder();

        $this->postWebhook($this->sessionPayload($order), signature: 't=1,v1=garbage')->assertStatus(400);

        $order->refresh();
        $this->assertSame('pending', $order->payment_status);
        Mail::assertNothingSent();
    }

    public function test_completed_session_marks_the_order_paid_once(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        $payload = $this->sessionPayload($order);

        $this->postWebhook($payload)->assertOk();

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertNull($order->expires_at);
        $this->assertSame('pi_test_1', $order->stripe_payment_intent_id);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(
            1,
            Activity::where('description', 'paid')->count(),
            'The paid transition must be recorded in the audit trail.',
        );

        // Stripe redelivers events — a replay must be a harmless no-op.
        $this->postWebhook($payload)->assertOk();

        Mail::assertSent(OrderConfirmationMail::class, 1);
        Mail::assertSent(OwnerAlertMail::class, 1);
    }

    public function test_completed_session_with_unpaid_status_waits_for_the_async_event(): void
    {
        Mail::fake();
        $order = $this->makeOrder();

        // FPX/GrabPay: checkout.session.completed can arrive while the payment
        // is still settling — only async_payment_succeeded confirms it.
        $this->postWebhook($this->sessionPayload($order, overrides: ['payment_status' => 'unpaid']))->assertOk();
        $this->assertSame('pending', $order->refresh()->payment_status);

        $this->postWebhook($this->sessionPayload($order, type: 'checkout.session.async_payment_succeeded'))->assertOk();
        $this->assertSame('paid', $order->refresh()->payment_status);
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }

    public function test_async_payment_failure_alerts_the_owner_and_keeps_the_order_pending(): void
    {
        Mail::fake();
        $order = $this->makeOrder();

        $this->postWebhook($this->sessionPayload($order, type: 'checkout.session.async_payment_failed', overrides: ['payment_status' => 'unpaid']))->assertOk();

        $order->refresh();
        $this->assertSame('pending', $order->payment_status);
        // The dead session must be detached: while it stayed recorded, the
        // payment page kept reporting "still being confirmed" and refused to
        // mint a fresh session — the customer could never retry.
        $this->assertNull($order->stripe_session_id);
        Mail::assertSent(OwnerAlertMail::class, 1);
        Mail::assertNotSent(OrderConfirmationMail::class);
    }

    public function test_async_payment_failure_of_an_old_session_never_drops_a_newer_one(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        // The customer already switched methods / retried: the order now holds
        // a NEWER session than the one this failure event is about.
        $order->update(['stripe_session_id' => 'cs_test_newer']);

        $this->postWebhook($this->sessionPayload($order, type: 'checkout.session.async_payment_failed', overrides: [
            'id' => 'cs_test_1', // the old, failed session
            'payment_status' => 'unpaid',
        ]))->assertOk();

        $this->assertSame('cs_test_newer', $order->refresh()->stripe_session_id);
    }

    public function test_payment_for_a_cancelled_order_never_uncancels_and_flags_a_manual_refund(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        $order->update(['status' => 'cancelled', 'cancelled_by' => 'system']);
        $stockBefore = Product::first()->stock;

        $this->postWebhook($this->sessionPayload($order))->assertOk();

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame($stockBefore, Product::first()->stock);
        Mail::assertSent(OwnerAlertMail::class, 1);
        Mail::assertNotSent(OrderConfirmationMail::class);
    }

    public function test_unknown_order_and_unhandled_event_types_are_acknowledged_quietly(): void
    {
        Mail::fake();
        $order = $this->makeOrder();

        // Unknown order reference → 200 so Stripe stops retrying, nothing changes.
        $this->postWebhook($this->sessionPayload($order, overrides: [
            'id' => 'cs_test_other',
            'client_reference_id' => 'ORD-2099-99999',
            'metadata' => ['order_id' => '999999'],
        ]))->assertOk();

        // Unhandled event type → acknowledged without touching anything.
        $this->postWebhook($this->sessionPayload($order, type: 'payment_intent.created'))->assertOk();

        $this->assertSame('pending', $order->refresh()->payment_status);
        Mail::assertNothingSent();
    }

    public function test_a_second_different_payment_for_a_paid_order_alerts_the_owner(): void
    {
        Mail::fake();
        $order = $this->makeOrder();

        // First payment settles the order with pi_test_1 (and sends the normal
        // "New order paid" owner alert).
        $this->postWebhook($this->sessionPayload($order))->assertOk();

        // A second, different successful payment for the same order (session
        // race / stale-session repay) must be flagged for a manual refund —
        // silently returning 200 would leave the double charge invisible.
        $this->postWebhook($this->sessionPayload($order, overrides: [
            'id' => 'cs_test_2',
            'payment_intent' => 'pi_test_2',
        ]))->assertOk();

        $order->refresh();
        $this->assertSame('pi_test_1', $order->stripe_payment_intent_id, 'The first payment stays the order\'s payment of record.');
        Mail::assertSent(OrderConfirmationMail::class, 1);
        Mail::assertSent(OwnerAlertMail::class, 2); // paid alert + duplicate alert
    }

    public function test_rejects_outright_when_the_webhook_secret_is_not_configured(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        config(['services.stripe.webhook_secret' => '']);

        $this->postWebhook($this->sessionPayload($order))->assertStatus(400);

        $this->assertSame('pending', $order->refresh()->payment_status);
        Mail::assertNothingSent();
    }

    public function test_settles_an_order_past_its_window_but_not_yet_cancelled(): void
    {
        Mail::fake();
        // Past expires_at but the expiry job hasn't cancelled it yet: the money
        // moved, so the webhook must settle rather than strand the payment.
        $order = $this->makeOrder(now()->subMinute());

        $this->postWebhook($this->sessionPayload($order))->assertOk();

        $this->assertSame('paid', $order->refresh()->payment_status);
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }
}
