<?php

namespace App\Console\Commands;

use App\Mail\OrderCancelledMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Cancel unpaid orders past their payment window and release reserved stock.';

    public function handle(): int
    {
        $orders = Order::where('payment_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->pluck('id');

        $count = 0;

        foreach ($orders as $id) {
            $expired = DB::transaction(function () use ($id, &$count) {
                $order = Order::where('id', $id)->lockForUpdate()->with('items')->first();

                // Re-check under lock so we never double-restock (the on-page
                // timer may have expired it already via PaymentPage::expireOrder()).
                if (! $order || $order->payment_status !== 'pending' || $order->status === 'cancelled') {
                    return null;
                }

                $order->restockItems();
                $order->update([
                    'status'              => 'cancelled',
                    'cancelled_by'        => 'system',
                    'cancellation_reason' => 'Order expired — payment not completed within 15 minutes',
                ]);
                $count++;

                return $order;
            });

            if (! $expired) {
                continue;
            }

            // Emails sent outside the transaction so a mail failure never rolls
            // back an already-committed cancellation.
            if (filled($expired->customer_email)) {
                try {
                    Mail::to($expired->customer_email)->send(new OrderCancelledMail($expired));
                } catch (\Throwable $e) {
                    logger()->error("Order expiry email failed for {$expired->order_number}: " . $e->getMessage());
                }
            }

            $ownerEmail = OwnerAlertMail::recipient();
            if ($ownerEmail) {
                try {
                    Mail::to($ownerEmail)->send(new OwnerAlertMail(
                        'Order auto-expired (not paid)',
                        [
                            'Order'    => $expired->order_number,
                            'Customer' => $expired->customer_name,
                            'Total'    => 'RM ' . number_format((float) $expired->total_amount, 2),
                            'Reason'   => 'Payment timer ran out',
                        ],
                        url('/admin/orders/' . $expired->getKey() . '/edit'),
                        'View order',
                    ));
                } catch (\Throwable $e) {
                    logger()->error("Order expiry owner alert failed for {$expired->order_number}: " . $e->getMessage());
                }
            }
        }

        $this->info("Expired {$count} unpaid order(s).");

        return self::SUCCESS;
    }
}
