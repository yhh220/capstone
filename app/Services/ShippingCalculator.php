<?php

namespace App\Services;

/**
 * Subtotal-based shipping, configured from the admin Settings:
 *   SHIPPING_FLAT_RATE      — flat fee charged below the free threshold
 *   SHIPPING_FREE_THRESHOLD — spend this much (or more) and shipping is free
 *
 * Set the threshold to 0 to disable free shipping (always charge the flat rate);
 * set the flat rate to 0 for always-free shipping.
 */
class ShippingCalculator
{
    public function flatRate(): float
    {
        return max(0, (float) setting('SHIPPING_FLAT_RATE', 0));
    }

    public function freeThreshold(): float
    {
        return max(0, (float) setting('SHIPPING_FREE_THRESHOLD', 0));
    }

    /** Shipping fee for a given cart subtotal. */
    public function fee(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $threshold = $this->freeThreshold();

        if ($threshold > 0 && $subtotal >= $threshold) {
            return 0.0;
        }

        return $this->flatRate();
    }

    /** How much more to spend to unlock free shipping (0 if already free / no threshold). */
    public function amountToFreeShipping(float $subtotal): float
    {
        $threshold = $this->freeThreshold();

        if ($threshold <= 0 || $subtotal >= $threshold || $this->flatRate() <= 0) {
            return 0.0;
        }

        return round($threshold - $subtotal, 2);
    }
}
