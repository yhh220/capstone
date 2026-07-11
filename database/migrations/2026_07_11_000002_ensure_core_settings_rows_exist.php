<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Guarantee every setting the admin panel documents actually exists as a row.
 * The shipping and cancellation keys were only ever inserted by the database
 * seeder, so any environment migrated without a full re-seed silently lacked
 * them — and since SettingResource has no create page (only the owner may
 * create settings, and only in code), those settings were IMPOSSIBLE to edit
 * from the panel: the code just used its hardcoded fallbacks. insertOrIgnore
 * keeps existing values untouched everywhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            ['key' => 'SHIPPING_FLAT_RATE', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'SHIPPING_FREE_THRESHOLD', 'value' => '300', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'CANCELLATION_FULL_REFUND_HOURS', 'value' => '24', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'CANCELLATION_FEE_PERCENT', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        // Intentionally no-op: these are live operating parameters the owner
        // may have edited; rolling back the migration must not delete them.
    }
};
