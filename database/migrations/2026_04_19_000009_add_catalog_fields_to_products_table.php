<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'description_ms')) {
                $table->longText('description_ms')->nullable()->after('description');
            }

            if (!Schema::hasColumn('products', 'description_zh')) {
                $table->longText('description_zh')->nullable()->after('description_ms');
            }

            if (!Schema::hasColumn('products', 'specs')) {
                $table->json('specs')->nullable()->after('images');
            }

            if (!Schema::hasColumn('products', 'compatible_vehicles')) {
                $table->json('compatible_vehicles')->nullable()->after('specs');
            }

            if (!Schema::hasColumn('products', 'model_url')) {
                $table->string('model_url')->nullable()->after('compatible_vehicles');
            }

            if (!Schema::hasColumn('products', 'has_3d')) {
                $table->boolean('has_3d')->default(false)->after('model_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'description_ms',
                'description_zh',
                'specs',
                'compatible_vehicles',
                'model_url',
                'has_3d',
            ]);
        });
    }
};
