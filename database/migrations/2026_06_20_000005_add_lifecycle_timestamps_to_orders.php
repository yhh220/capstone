<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // When the order was paid / shipped / cancelled — for reporting, audit
            // and support, instead of inferring from the status string alone.
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->timestamp('shipped_at')->nullable()->after('paid_at');
            $table->timestamp('cancelled_at')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'shipped_at', 'cancelled_at']);
        });
    }
};
