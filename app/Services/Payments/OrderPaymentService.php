<?php

namespace App\Services\Payments;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Support\Breadcrumbs;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * The single settle step every payment path funnels into: the demo pay button,
 * the Stripe webhook and the Stripe success-URL verification all call markPaid(),
 * so the paid transition (and its confirmation email) can only ever happen once
 * per order no matter how many sources race or replay.
 */
class OrderPaymentService
{
    /**
     * Idempotently transition an order to paid/processing.
     *
     * $allowExpired is set by the Stripe sources: once Stripe reports the money
     * moved, an order past its payment window (but not yet cancelled by the
     * expiry job) must still settle rather than strand a real payment.
     *
     * @return string 'paid' | 'already_paid' | 'cancelled' | 'expired'
     */
    public function markPaid(
        Order $order,
        string $source,
        ?string $paymentIntentId = null,
        ?Authenticatable $causer = null,
        bool $allowExpired = false,
    ): string {
        // Layer 1 — atomic lock (works on the database cache store; Redis if set).
        // Same key across all sources, so a webhook and a success-URL return
        // racing each other contend here first.
        $lock = Cache::lock('pay-order:'.$order->id, 10);

        if (! $lock->get()) {
            return 'already_paid';
        }

        try {
            // Layer 2 — pessimistic row lock inside a transaction, and Layer 3 —
            // an atomic conditional flip whose affected-row count is the single-
            // winner / "unique-index" bottom guard: only the request that finds the
            // order still 'pending' transitions it, so the email can fire only once.
            $result = DB::transaction(function () use ($order, $paymentIntentId, $allowExpired) {
                $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                if (! $locked || ! $locked->isAwaitingPayment()) {
                    return $locked && $locked->status === 'cancelled' ? 'cancelled' : 'already_paid';
                }
                if ($locked->isPaymentExpired() && ! $allowExpired) {
                    return 'expired';
                }

                $updates = [
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'expires_at' => null,
                    'paid_at' => now(),
                ];
                if ($paymentIntentId !== null) {
                    $updates['stripe_payment_intent_id'] = $paymentIntentId;
                }

                $affected = Order::where('id', $locked->id)
                    ->where('payment_status', 'pending')
                    ->where('status', '!=', 'cancelled')
                    ->update($updates);

                return $affected === 1 ? 'paid' : 'already_paid';
            });
        } finally {
            $lock->release();
        }

        if ($result !== 'paid') {
            return $result;
        }

        $order->refresh();

        Breadcrumbs::push('payment', 'Order paid', [
            'order' => $order->order_number,
            'source' => $source,
        ]);

        // The atomic builder UPDATE bypasses Eloquent model events, so spatie/activitylog
        // would not see this state change. Log it explicitly so the audit trail reflects
        // the most security-sensitive transition in the system.
        activity('order')
            ->performedOn($order)
            ->causedBy($causer)
            ->withProperties([
                'payment_status' => 'paid',
                'status' => 'processing',
                'source' => $source,
            ])
            ->log('paid');

        try {
            Breadcrumbs::push('mail', 'Sending order confirmation');
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order->fresh('items')));
        } catch (\Throwable $e) {
            logger()->error('Order confirmation email failed: '.$e->getMessage());
        }

        return 'paid';
    }
}
