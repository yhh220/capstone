<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Seeds the PAYMENT_MODE row ('demo' | 'stripe') so the admin Settings list can
// edit it on existing databases. 'demo' keeps the original simulated payment;
// 'stripe' routes card / FPX / GrabPay orders through Stripe Checkout (test
// mode) while unsupported e-wallets stay simulated. insertOrIgnore keeps this
// safe to re-run and a no-op where the row already exists.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'PAYMENT_MODE',
                'value' => 'demo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'PAYMENT_MODE')->delete();
    }
};
