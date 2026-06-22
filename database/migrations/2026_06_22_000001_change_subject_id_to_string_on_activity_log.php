<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // nullableMorphs() defaults subject_id to unsignedBigInteger, which
        // assumes every loggable model has a normal auto-increment id. The
        // Setting model uses a string primary key ('key', e.g.
        // "ONLINE_SHOPPING_ENABLED"), so logging a Setting change fails with
        // SQLSTATE 1366 on strict-typed MySQL/TiDB (SQLite silently allowed
        // it because it doesn't enforce column types).
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('subject_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }
};
