<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'vehicle_model')) {
                $table->string('vehicle_model')->nullable()->after('customer_email');
            }

            if (!Schema::hasColumn('bookings', 'vehicle_plate')) {
                $table->string('vehicle_plate')->nullable()->after('vehicle_model');
            }

            if (!Schema::hasColumn('bookings', 'confirm_token')) {
                $table->string('confirm_token')->nullable()->after('status');
            }

            if (!Schema::hasColumn('bookings', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('preferred_time');
            }

            if (!Schema::hasColumn('bookings', 'end_at')) {
                $table->dateTime('end_at')->nullable()->after('start_at');
            }
        });

        DB::table('bookings')
            ->whereNull('confirm_token')
            ->orderBy('id')
            ->each(function (object $booking): void {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['confirm_token' => Str::random(32)]);
            });

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'confirm_token')) {
                $table->unique('confirm_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['confirm_token']);
            $table->dropColumn(['vehicle_model', 'vehicle_plate', 'confirm_token', 'start_at', 'end_at']);
        });
    }
};
