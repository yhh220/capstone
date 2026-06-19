<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('level');       // Monolog numeric level
            $table->string('level_name', 20)->index();   // debug … emergency
            $table->text('message');
            $table->string('channel', 60)->nullable();
            $table->string('trace_id', 40)->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('method', 10)->nullable();
            $table->string('path', 255)->nullable();
            $table->json('context')->nullable();         // context + breadcrumbs + extra
            $table->timestamp('logged_at')->index();
            $table->timestamp('created_at')->nullable(); // for Prunable

            $table->index(['level_name', 'logged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_logs');
    }
};
