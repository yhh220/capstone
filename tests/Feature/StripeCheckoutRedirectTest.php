<?php

namespace Tests\Feature;

use App\Livewire\PaymentPage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\Payments\StripeCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Tests\TestCase;

class StripeCheckoutRedirectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.test', 'password' => 'password', 'role' => 'client']);
    }

    private function makeOrder(string $paymentMethod): Order
    {
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp-'.uniqid(), 'price' => 300, 'stock' => 1, 'is_active' => true]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $this->user->name,
            'customer_email' => $this->user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1 Jln', 'city' => 'KL', 'postcode' => '50000', 'state' => 'KL'],
            'subtotal' => 600,
            'shipping_fee' => 0,
            'total_amount' => 600,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $paymentMethod,
            'expires_at' => now()->addMinutes(15),
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

    public function test_stripe_mode_redirects_an_fpx_order_to_the_hosted_session(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        $order = $this->makeOrder('FPX - Maybank2u');

        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('createSession')->once()->andReturn(Session::constructFrom([
                'id' => 'cs_test_1',
                'url' => 'https://checkout.stripe.test/pay/cs_test_1',
                'payment_status' => 'unpaid',
                'expires_at' => time() + 1800,
            ]));
        });

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay')
            ->assertRedirect('https://checkout.stripe.test/pay/cs_test_1');

        // The redirect is not the confirmation — only the webhook / verified
        // success return may settle the order.
        $this->assertSame('pending', $order->refresh()->payment_status);
    }

    public function test_stripe_mode_keeps_unsupported_wallets_on_the_demo_flow(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        $order = $this->makeOrder("Touch 'n Go eWallet");

        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('createSession')->never();
        });

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay');

        $this->assertSame('paid', $order->refresh()->payment_status);
    }

    public function test_demo_mode_never_calls_stripe_even_for_card_orders(): void
    {
        // PAYMENT_MODE defaults to 'demo' (seeded by migration) — no mock, the
        // real service must short-circuit before touching the SDK.
        $order = $this->makeOrder('Credit / Debit Card');

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay');

        $this->assertSame('paid', $order->refresh()->payment_status);
    }

    public function test_stripe_mode_without_a_test_secret_falls_back_to_demo(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        config(['services.stripe.secret' => '']); // misconfigured: no key
        $order = $this->makeOrder('Credit / Debit Card');

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay');

        $this->assertSame('paid', $order->refresh()->payment_status);
    }

    public function test_an_already_paid_session_settles_instead_of_minting_a_new_charge(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        $order = $this->makeOrder('Credit / Debit Card');
        $order->update(['stripe_session_id' => 'cs_test_paid']);

        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('retrieveSession')->once()->with('cs_test_paid')->andReturn(Session::constructFrom([
                'id' => 'cs_test_paid',
                'status' => 'complete',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_9',
            ]));
            $mock->shouldReceive('createSession')->never();
        });

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay');

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pi_test_9', $order->stripe_payment_intent_id);
    }

    public function test_a_completed_but_unsettled_session_shows_processing_instead_of_recharging(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        $order = $this->makeOrder('FPX - Maybank2u');
        $order->update(['stripe_session_id' => 'cs_test_settling']);

        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('retrieveSession')->once()->with('cs_test_settling')->andReturn(Session::constructFrom([
                'id' => 'cs_test_settling',
                'status' => 'complete', // checkout finished…
                'payment_status' => 'unpaid', // …but FPX hasn't settled yet
                'payment_intent' => 'pi_test_9',
            ]));
            $mock->shouldReceive('createSession')->never(); // a new session here would invite a double charge
        });

        Livewire::actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $order->order_number])
            ->call('pay')
            ->assertSet('paymentProcessing', true);

        $this->assertSame('pending', $order->refresh()->payment_status);
    }

    public function test_a_live_secret_is_refused_by_design(): void
    {
        Setting::setValue('PAYMENT_MODE', 'stripe');
        config(['services.stripe.secret' => 'sk_live_should_never_be_used']);

        $this->assertFalse(app(StripeCheckoutService::class)->enabled());
    }
}
