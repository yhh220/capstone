<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\ShippingCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_flat_rate_below_threshold_and_free_above(): void
    {
        Setting::updateOrCreate(['key' => 'SHIPPING_FLAT_RATE'], ['value' => '10']);
        Setting::updateOrCreate(['key' => 'SHIPPING_FREE_THRESHOLD'], ['value' => '300']);
        cache()->flush();

        $calc = app(ShippingCalculator::class);

        $this->assertEqualsWithDelta(10.0, $calc->fee(250), 0.001, 'below threshold → flat rate');
        $this->assertEqualsWithDelta(0.0, $calc->fee(300), 0.001, 'at threshold → free');
        $this->assertEqualsWithDelta(0.0, $calc->fee(350), 0.001, 'above threshold → free');
        $this->assertEqualsWithDelta(0.0, $calc->fee(0), 0.001, 'empty cart → no shipping');
        $this->assertEqualsWithDelta(50.0, $calc->amountToFreeShipping(250), 0.001);
        $this->assertEqualsWithDelta(0.0, $calc->amountToFreeShipping(300), 0.001);
    }

    public function test_no_settings_means_no_shipping_charge(): void
    {
        cache()->flush();
        $calc = app(ShippingCalculator::class);

        // Defaults (no rows) → flat 0, threshold 0 → always free.
        $this->assertEqualsWithDelta(0.0, $calc->fee(250), 0.001);
    }
}
