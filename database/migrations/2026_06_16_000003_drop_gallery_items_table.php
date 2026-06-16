<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the Gallery feature entirely (public page + admin resource were
 * deleted). Drops the gallery_items table and clears any Spatie MediaLibrary
 * rows that pointed at gallery items so no orphaned media is left behind.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media')) {
            DB::table('media')->where('model_type', 'App\\Models\\GalleryItem')->delete();
        }

        Schema::dropIfExists('gallery_items');
    }

    public function down(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->default('general');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
