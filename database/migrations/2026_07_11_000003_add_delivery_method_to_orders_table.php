<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Store pickup at checkout: orders now record how the customer receives them.
 * 'delivery' (courier to a West Malaysia address, the only mode until now) or
 * 'pickup' (free self-collection at the Shah Alam showroom, no address
 * required). Default keeps every existing order truthful.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_method', 20)->default('delivery')->after('shipping_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_method');
        });
    }
};
