<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns the orders table with what the code expects, and adds the payment
 * deadline used by the 15-minute checkout timer.
 *
 * Some databases ended up with a legacy `total` column instead of the
 * `total_amount` the app uses everywhere, and were missing `tracking_number`.
 * This repair is idempotent: a no-op on healthy/fresh installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Legacy `total` → `total_amount` (only if the table is in the old shape).
        if (Schema::hasColumn('orders', 'total') && ! Schema::hasColumn('orders', 'total_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->renameColumn('total', 'total_amount');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('order_number');
            }
            // Shipping fee was never in the canonical migrations on some installs.
            if (! Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 10, 2)->default(0)->after('subtotal');
            }
            // Payment deadline — when an unpaid order auto-cancels (15-min timer).
            if (! Schema::hasColumn('orders', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
