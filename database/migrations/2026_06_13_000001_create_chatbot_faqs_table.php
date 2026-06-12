<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('topic');                       // admin-facing label, e.g. "Window tint warranty"
            $table->json('keywords');                      // trigger words/phrases (any language)
            $table->unsignedSmallInteger('priority')->default(50); // higher beats lower when several rules match
            $table->text('reply_en');
            $table->text('reply_ms')->nullable();
            $table->text('reply_zh')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_faqs');
    }
};
