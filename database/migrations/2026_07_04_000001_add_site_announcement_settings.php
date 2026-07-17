<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Seeds the two rows behind the site-wide announcement bar so the admin Settings
// list can edit them on existing databases (the DatabaseSeeder only runs on a
// fresh seed). insertOrIgnore keeps this safe to re-run and a no-op where the
// rows already exist.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'SITE_ANNOUNCEMENT_ENABLED',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'SITE_ANNOUNCEMENT_TEXT',
                'value' => 'Online shopping and sign-in are temporarily under maintenance. You can still browse our products and book an in-store appointment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', ['SITE_ANNOUNCEMENT_ENABLED', 'SITE_ANNOUNCEMENT_TEXT'])
            ->delete();
    }
};
