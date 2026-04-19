<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'duration_minutes')) {
                $table->unsignedInteger('duration_minutes')->default(60)->after('description');
            }

            if (!Schema::hasColumn('services', 'buffer_after')) {
                $table->unsignedInteger('buffer_after')->default(15)->after('duration_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'buffer_after']);
        });
    }
};
