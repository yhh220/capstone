<?php

namespace App\Services;

use App\Models\Order;

/**
 * Tiered cancellation refund, configured from the admin Settings:
 *   CANCELLATION_FULL_REFUND_HOURS — hours after payment a cancelled order still gets 100%
 *   CANCELLATION_FEE_PERCENT       — fee deducted from the refund once that window has passed
 *
 * Once an order has shipped or delivered, self-service cancellation is blocked entirely —
 * that's not a worse tier, it's a wall (the existing returns/exchange flow handles that case).
 */
class RefundCalculator
{
    public function fullRefundHours(): int
    {
        return max(0, (int) setting('CANCELLATION_FULL_REFUND_HOURS', 24));
    }

    public function feePercent(): float
    {
        return min(100, max(0, (float) setting('CANCELLATION_FEE_PERCENT', 10)));
    }

    public function isCancellable(Order $order): bool
    {
        return ! in_array($order->status, ['shipped', 'delivered', 'cancelled'], true);
    }

    /**
     * Tiered refund for a paid order being cancelled right now. Null if the order isn't
     * paid, or is no longer cancellable (shipped/delivered/already cancelled).
     *
     * @return array{tier: 'full'|'fee', percentage: float, amount: float}|null
     */
    public function calculate(Order $order): ?array
    {
        if ($order->payment_status !== 'paid' || $order->paid_at === null) {
            return null;
        }

        if (! $this->isCancellable($order)) {
            return null;
        }

        // Cast to int: Carbon 3 returns float from diffInHours; truncating gives correct
        // whole-hour comparison (e.g. 24.0002 elapsed → 24 whole hours → still within window).
        $hoursSincePaid = (int) $order->paid_at->diffInHours(now(), absolute: true);
        $withinFullWindow = $hoursSincePaid <= $this->fullRefundHours();
        $percentage = $withinFullWindow ? 100.0 : (100.0 - $this->feePercent());

        return [
            'tier'       => $withinFullWindow ? 'full' : 'fee',
            'percentage' => $percentage,
            'amount'     => round(((float) $order->total_amount) * $percentage / 100, 2),
        ];
    }

    /**
     * Plain-language status for a standing badge — what would happen if this order were
     * cancelled right now. Independent of whether anyone is actually about to cancel it.
     */
    public function eligibilityLabel(Order $order): string
    {
        if ($order->payment_status !== 'paid') {
            return 'Not yet paid — cancelling now is free, nothing to refund.';
        }

        if (! $this->isCancellable($order)) {
            return 'Not eligible for refund — order has shipped.';
        }

        $refund = $this->calculate($order);

        // Defensive: paid orders always get paid_at stamped by every code path,
        // but a row edited outside the app (import, manual SQL) without one
        // must degrade to a neutral label, not crash the whole orders table.
        if ($refund === null) {
            return 'Refund window unknown — no payment date recorded.';
        }

        return $refund['tier'] === 'full'
            ? 'Eligible for full refund if cancelled now.'
            : "Eligible for a {$refund['percentage']}% refund if cancelled now ({$this->feePercent()}% fee applies).";
    }
}
