<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * New sign-ups now confirm their email via OTP before the account is created,
     * so every freshly registered user is verified. Backfill existing accounts
     * (seeded admin, pre-OTP registrations) as verified so the new flow never
     * locks anyone out.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // No-op: we don't un-verify accounts on rollback (would be destructive).
    }
};
