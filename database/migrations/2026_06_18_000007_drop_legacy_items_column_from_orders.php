<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the legacy NOT-NULL `items` JSON column some installs have on `orders`.
 * Line items live in the `order_items` table (the `items()` relationship), so
 * the column is dead weight — and being NOT NULL it actually blocks order
 * creation. No-op on databases that never had it.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'items')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('items');
            });
        }
    }

    public function down(): void
    {
        // Intentionally not recreated — it was unused legacy data.
    }
};
