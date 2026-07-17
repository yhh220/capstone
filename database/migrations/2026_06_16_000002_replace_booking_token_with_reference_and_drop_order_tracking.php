<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces opaque IDs with human-friendly references.
 *
 *  - bookings.confirm_token (random UUID magic-link) → bookings.reference
 *    (BK-YYYY-NNNNN), the value a customer quotes with their phone number to
 *    look up or cancel a booking — mirroring how orders use order_number + email.
 *  - orders.tracking_number (WNWN……, never actually used to look anything up)
 *    is dropped entirely; the order_number is the single tracking identifier.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── bookings: add the reference column ──────────────────────────────
        if (! Schema::hasColumn('bookings', 'reference')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('reference')->nullable()->after('id');
            });
        }

        // Backfill existing rows with BK-YYYY-NNNNN, sequential per calendar year.
        $counters = [];
        DB::table('bookings')->orderBy('id')->get()->each(function ($booking) use (&$counters) {
            if (! empty($booking->reference)) {
                return;
            }
            $year = $booking->created_at ? date('Y', strtotime($booking->created_at)) : date('Y');
            $counters[$year] = ($counters[$year] ?? 0) + 1;
            $reference = 'BK-'.$year.'-'.str_pad((string) $counters[$year], 5, '0', STR_PAD_LEFT);
            DB::table('bookings')->where('id', $booking->id)->update(['reference' => $reference]);
        });

        if (! $this->indexExists('bookings', 'bookings_reference_unique')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->unique('reference', 'bookings_reference_unique'));
        }

        // ── bookings: drop the old confirm_token ────────────────────────────
        if (Schema::hasColumn('bookings', 'confirm_token')) {
            if ($this->indexExists('bookings', 'bookings_confirm_token_unique')) {
                Schema::table('bookings', fn (Blueprint $table) => $table->dropUnique('bookings_confirm_token_unique'));
            }
            Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn('confirm_token'));
        }

        // ── orders: drop the unused tracking_number ─────────────────────────
        if (Schema::hasColumn('orders', 'tracking_number')) {
            if ($this->indexExists('orders', 'orders_tracking_number_unique')) {
                Schema::table('orders', fn (Blueprint $table) => $table->dropUnique('orders_tracking_number_unique'));
            }
            Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('tracking_number'));
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('bookings', 'confirm_token')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('confirm_token')->nullable()->after('status');
            });
        }

        if (Schema::hasColumn('bookings', 'reference')) {
            if ($this->indexExists('bookings', 'bookings_reference_unique')) {
                Schema::table('bookings', fn (Blueprint $table) => $table->dropUnique('bookings_reference_unique'));
            }
            Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn('reference'));
        }

        if (! Schema::hasColumn('orders', 'tracking_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('tracking_number')->nullable()->after('order_number');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn (array $index) => $index['name'] === $indexName);
    }
};
