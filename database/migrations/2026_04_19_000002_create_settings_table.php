<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) return;

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->timestamps();
        });

        // Seed default settings
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'ONLINE_SHOPPING_ENABLED', 'value' => 'false', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_START',    'value' => '09:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_END',      'value' => '18:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
