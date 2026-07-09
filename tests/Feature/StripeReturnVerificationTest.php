<?php

namespace Tests\Feature;

use App\Livewire\PaymentPage;
use App\Mail\OrderConfirmationMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Payments\StripeCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Tests\TestCase;

class StripeReturnVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.test', 'password' => 'password', 'role' => 'client']);
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp', 'price' => 300, 'stock' => 1, 'is_active' => true]);

        $this->order = Order::create([
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
            'payment_method' => 'FPX - Maybank2u',
            'stripe_session_id' => 'cs_test_1',
            'expires_at' => now()->addMinutes(30),
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 300,
            'subtotal' => 600,
        ]);
    }

    public function test_a_paid_session_on_return_settles_the_order_without_a_webhook(): void
    {
        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('retrieveSession')->once()->with('cs_test_1')->andReturn(Session::constructFrom([
                'id' => 'cs_test_1',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_1',
            ]));
        });

        Livewire::withQueryParams(['session_id' => 'cs_test_1'])
            ->actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $this->order->order_number]);

        $this->order->refresh();
        $this->assertSame('paid', $this->order->payment_status);
        $this->assertSame('pi_test_1', $this->order->stripe_payment_intent_id);
        Mail::assertSent(OrderConfirmationMail::class, 1);
        Mail::assertSent(OwnerAlertMail::class, 1);
    }

    public function test_a_still_settling_session_shows_the_processing_notice(): void
    {
        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('retrieveSession')->once()->with('cs_test_1')->andReturn(Session::constructFrom([
                'id' => 'cs_test_1',
                'payment_status' => 'unpaid',
                'payment_intent' => 'pi_test_1',
            ]));
        });

        Livewire::withQueryParams(['session_id' => 'cs_test_1'])
            ->actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $this->order->order_number])
            ->assertSet('paymentProcessing', true);

        $this->assertSame('pending', $this->order->refresh()->payment_status);
        Mail::assertNotSent(OrderConfirmationMail::class);
    }

    public function test_polling_settles_the_order_once_the_bank_confirms(): void
    {
        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            // First retrieval (mount): still settling. Second (wire:poll): paid.
            $mock->shouldReceive('retrieveSession')->twice()->with('cs_test_1')->andReturn(
                Session::constructFrom(['id' => 'cs_test_1', 'payment_status' => 'unpaid', 'payment_intent' => 'pi_test_1']),
                Session::constructFrom(['id' => 'cs_test_1', 'payment_status' => 'paid', 'payment_intent' => 'pi_test_1']),
            );
        });

        Livewire::withQueryParams(['session_id' => 'cs_test_1'])
            ->actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $this->order->order_number])
            ->assertSet('paymentProcessing', true)
            ->call('pollPaymentStatus');

        $this->assertSame('paid', $this->order->refresh()->payment_status);
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }

    public function test_a_session_id_that_does_not_match_the_order_is_never_looked_up(): void
    {
        $this->mock(StripeCheckoutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('retrieveSession')->never();
        });

        Livewire::withQueryParams(['session_id' => 'cs_evil_other'])
            ->actingAs($this->user)
            ->test(PaymentPage::class, ['orderNumber' => $this->order->order_number]);

        $this->assertSame('pending', $this->order->refresh()->payment_status);
    }
}
