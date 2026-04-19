<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_logs')) {
            return;
        }

        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->string('driver', 50);
            $table->string('feature', 100);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('status', 20)->default('success');
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['driver', 'feature']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
