<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gallery images moved to Spatie MediaLibrary (the `images` collection,
     * stored in the `media` table). The original `image` string column is now
     * a legacy field that the admin form never writes, so its NOT NULL
     * constraint made every "create gallery item" insert fail. Make it
     * nullable so MediaLibrary-backed items can be saved.
     */
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};
