<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // ExpireUnpaidOrders runs every minute with WHERE payment_status='pending'
            // AND expires_at < now(). Composite index covers both predicates together.
            $table->index(['payment_status', 'expires_at'], 'idx_orders_payment_status_expires_at');

            // PaymentPage and MyAccountPage filter by status independently.
            $table->index('status', 'idx_orders_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_payment_status_expires_at');
            $table->dropIndex('idx_orders_status');
        });
    }
};
