<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Social-login accounts have no password — store NULL instead of a random
        // throwaway, so the app can tell "no password set" from "has a password".
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        // One-time normalisation: every account that currently has a linked social
        // account got there via OAuth (random password) before this existed — null
        // those so they show the "Set password" flow instead of "Change password".
        $socialUserIds = DB::table('social_accounts')->distinct()->pluck('user_id');
        if ($socialUserIds->isNotEmpty()) {
            DB::table('users')->whereIn('id', $socialUserIds)->update(['password' => null]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
