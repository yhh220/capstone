<?php

namespace Tests\Unit;

use App\Livewire\CheckoutPage;
use App\Services\Payments\StripeCheckoutService;
use PHPUnit\Framework\TestCase;

class StripePaymentMethodMapperTest extends TestCase
{
    public function test_every_fpx_bank_label_maps_to_fpx(): void
    {
        foreach (CheckoutPage::FPX_BANKS as $bank) {
            $this->assertSame(
                ['fpx'],
                StripeCheckoutService::paymentMethodTypesFor('FPX - '.$bank),
                "FPX - {$bank} should map to Stripe fpx",
            );
        }
    }

    public function test_grabpay_card_and_bare_fpx_map_to_their_stripe_types(): void
    {
        $this->assertSame(['grabpay'], StripeCheckoutService::paymentMethodTypesFor('GrabPay'));
        $this->assertSame(['card'], StripeCheckoutService::paymentMethodTypesFor('Credit / Debit Card'));
        // Stripe-mode FPX orders carry no bank suffix — the bank is chosen on
        // Stripe's hosted page.
        $this->assertSame(['fpx'], StripeCheckoutService::paymentMethodTypesFor('FPX'));
    }

    public function test_unsupported_wallets_and_legacy_labels_stay_on_the_demo_flow(): void
    {
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor("Touch 'n Go eWallet"));
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor('ShopeePay'));
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor('Boost'));
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor('online_banking')); // pre-migration default
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor(null));
        $this->assertNull(StripeCheckoutService::paymentMethodTypesFor(''));
    }
}
