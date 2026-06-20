<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancellation_reason', 255)->nullable()->after('cancelled_at');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('cancellation_reason');
            $table->decimal('refund_percentage', 5, 2)->nullable()->after('refund_amount');
            $table->string('cancelled_by', 20)->nullable()->after('refund_percentage');
            $table->timestamp('refunded_at')->nullable()->after('cancelled_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_reason',
                'refund_amount',
                'refund_percentage',
                'cancelled_by',
                'refunded_at',
            ]);
        });
    }
};
