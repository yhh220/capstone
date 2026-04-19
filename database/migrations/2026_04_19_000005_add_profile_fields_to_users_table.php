<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 10)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'address_line')) {
                $table->string('address_line')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address_line');
            }
            if (!Schema::hasColumn('users', 'postcode')) {
                $table->string('postcode', 10)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('postcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['phone', 'gender', 'address_line', 'city', 'postcode', 'state'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
