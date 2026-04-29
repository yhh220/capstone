<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id', 'idx_products_category_id');
            $table->index('is_active',   'idx_products_is_active');
            $table->index('created_at',  'idx_products_created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'idx_orders_user_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_items_order_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status',   'idx_bookings_status');
            $table->index('start_at', 'idx_bookings_start_at');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('is_read', 'idx_contacts_is_read');
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->index('is_active', 'idx_feedback_is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_is_active');
            $table->dropIndex('idx_products_created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_status');
            $table->dropIndex('idx_bookings_start_at');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('idx_contacts_is_read');
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex('idx_feedback_is_active');
        });
    }
};
