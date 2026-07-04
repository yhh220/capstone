<?php

namespace App\Services;

use App\Mail\OrderCancelledMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Reacts to online-shopping being switched off. Closing the shop clears the
 * board: every still-unpaid order is cancelled and its reserved stock released,
 * so the storefront never leaves a customer with a "pending payment" order they
 * can no longer pay for (the /pay route is gated by ShoppingEnabled). PAID
 * orders are deliberately untouched — those are real money owed a customer, and
 * are fulfilled/refunded through the normal admin flow regardless of shop mode.
 */
class ShopModeService
{
    /**
     * Cancel + restock all unpaid orders. Returns the number cancelled.
     * Safe to call repeatedly (already-cancelled/paid orders are skipped).
     */
    public function cancelUnpaidOrders(): int
    {
        $ids = Order::where('payment_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->pluck('id');

        $cancelled = [];

        foreach ($ids as $id) {
            // Re-check under a row lock so this never double-restocks against the
            // expiry scheduler or the on-page payment timer racing the same order.
            $order = DB::transaction(function () use ($id) {
                $order = Order::where('id', $id)->lockForUpdate()->with('items')->first();

                if (! $order || $order->payment_status !== 'pending' || $order->status === 'cancelled') {
                    return null;
                }

                $order->restockItems();
                $order->update([
                    'status'              => 'cancelled',
                    'cancelled_by'        => 'system',
                    'cancellation_reason' => 'Online shopping was turned off — unpaid order released',
                ]);

                return $order;
            });

            if ($order) {
                $cancelled[] = $order;
            }
        }

        // Emails outside the transactions so a mail failure never rolls back an
        // already-committed cancellation.
        foreach ($cancelled as $order) {
            if (filled($order->customer_email)) {
                try {
                    Mail::to($order->customer_email)->send(new OrderCancelledMail($order));
                } catch (\Throwable $e) {
                    logger()->error("Shop-close cancellation email failed for {$order->order_number}: " . $e->getMessage());
                }
            }
        }

        // One summary alert to the owner rather than one per order.
        if ($cancelled !== []) {
            $ownerEmail = config('services.store.email');
            if ($ownerEmail) {
                try {
                    Mail::to($ownerEmail)->send(new OwnerAlertMail(
                        'Online shopping turned off — unpaid orders released',
                        [
                            'Orders cancelled' => (string) count($cancelled),
                            'Reason'           => 'Shop switched to showroom mode; unpaid orders cannot be paid while it is off.',
                        ],
                        url('/admin/orders'),
                        'View orders',
                    ));
                } catch (\Throwable $e) {
                    logger()->error('Shop-close owner alert failed: ' . $e->getMessage());
                }
            }
        }

        return count($cancelled);
    }
}
