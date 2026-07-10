<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Services become admin-manageable (ServiceResource), so their translations
 * move from lang/ms.json + lang/zh.json __() keys — which only a developer
 * can edit, and which silently fall back to English the moment an admin
 * renames a service — into per-language columns, the same pattern products
 * already use. The backfill below copies each service's existing JSON
 * translations into the new columns so nothing is lost in the switch.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('name_ms')->nullable()->after('name');
            $table->string('name_zh')->nullable()->after('name_ms');
            $table->text('description_ms')->nullable()->after('description');
            $table->text('description_zh')->nullable()->after('description_ms');
        });

        foreach (['ms', 'zh'] as $locale) {
            $path = lang_path($locale.'.json');
            if (! is_file($path)) {
                continue;
            }

            $translations = json_decode((string) file_get_contents($path), true) ?: [];

            foreach (DB::table('services')->get(['id', 'name', 'description']) as $service) {
                DB::table('services')->where('id', $service->id)->update(array_filter([
                    'name_'.$locale => $translations[$service->name] ?? null,
                    'description_'.$locale => $translations[$service->description] ?? null,
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['name_ms', 'name_zh', 'description_ms', 'description_zh']);
        });
    }
};
