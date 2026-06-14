<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs the orders table on databases where the original e-commerce columns
 * never landed. The first migration declared order_number/tracking_number with
 * an inline ->unique() — SQLite cannot ALTER TABLE ADD a UNIQUE column, so on
 * SQLite that statement failed and the table was left missing every checkout
 * column (order_number, customer_*, subtotal, payment_status, …). Online
 * shopping would crash on the first order.
 *
 * Idempotent: each column/index is added only if missing, so healthy databases
 * (and fresh installs) are a no-op. Unique indexes are created separately,
 * which SQLite allows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable()->after('id');
            }
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('order_number');
            }
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_email');
            }
            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('customer_phone');
            }
            if (! Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('shipping_address');
            }
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('total_amount');
            }
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('payment_status');
            }
        });

        // Unique indexes added separately (SQLite-safe). Guarded so re-runs and
        // healthy databases don't error on a duplicate index name.
        $this->addUniqueIndex('orders', 'order_number', 'orders_order_number_unique');
        $this->addUniqueIndex('orders', 'tracking_number', 'orders_tracking_number_unique');
    }

    private function addUniqueIndex(string $tableName, string $column, string $indexName): void
    {
        if (! Schema::hasColumn($tableName, $column)) {
            return;
        }

        $exists = collect(Schema::getIndexes($tableName))
            ->contains(fn (array $index) => $index['name'] === $indexName);

        if (! $exists) {
            Schema::table($tableName, fn (Blueprint $table) => $table->unique($column, $indexName));
        }
    }

    public function down(): void
    {
        // Non-destructive repair — leave the columns in place on rollback.
    }
};
