<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Error grouping used to match rows with SUBSTR(message, 1, 100) = ? — an
// unindexed scan on every error write, and PHP's byte-based substr() disagreed
// with SQL's character-based SUBSTR() the moment a message contained multibyte
// text (Chinese, curly quotes), silently breaking regression-reopen and the
// recurrence checks. A precomputed, indexed fingerprint column fixes all three.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('app_logs', 'fingerprint')) {
                $table->string('fingerprint', 100)->nullable()->after('message');
                $table->index(['fingerprint', 'logged_at']);
            }
        });

        // Backfill: SQL SUBSTR counts characters (SQLite + MySQL/TiDB alike),
        // which matches the mb_substr() the handler now uses on write.
        DB::table('app_logs')
            ->whereNull('fingerprint')
            ->update(['fingerprint' => DB::raw('SUBSTR(message, 1, 100)')]);
    }

    public function down(): void
    {
        Schema::table('app_logs', function (Blueprint $table) {
            $table->dropIndex(['fingerprint', 'logged_at']);
            $table->dropColumn('fingerprint');
        });
    }
};
