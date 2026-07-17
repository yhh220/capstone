<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Completes the per-language column pattern started by description_ms/description_zh:
// the storefront shows name + short_description on every product card, so those two
// need MS/ZH variants as well. Empty columns fall back to the English value via the
// Product::translated* accessors.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'name_ms')) {
                $table->string('name_ms')->nullable()->after('name');
            }

            if (! Schema::hasColumn('products', 'name_zh')) {
                $table->string('name_zh')->nullable()->after('name_ms');
            }

            if (! Schema::hasColumn('products', 'short_description_ms')) {
                $table->text('short_description_ms')->nullable()->after('short_description');
            }

            if (! Schema::hasColumn('products', 'short_description_zh')) {
                $table->text('short_description_zh')->nullable()->after('short_description_ms');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_ms',
                'name_zh',
                'short_description_ms',
                'short_description_zh',
            ]);
        });
    }
};
