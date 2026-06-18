<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
            DB::transaction(function () use ($id, &$count) {
                $order = Order::where('id', $id)->lockForUpdate()->with('items')->first();

                // Re-check under lock so we never double-restock (the on-page
                // timer may have expired it already).
                if (! $order || $order->payment_status !== 'pending' || $order->status === 'cancelled') {
                    return;
                }

                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }

                $order->update(['status' => 'cancelled']);
                $count++;
            });
        }

        $this->info("Expired {$count} unpaid order(s).");

        return self::SUCCESS;
    }
}
