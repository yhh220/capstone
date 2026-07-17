<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Existing verified-purchase reviews should follow the immediate-publish policy. */
    public function up(): void
    {
        DB::table('product_reviews')->update(['is_approved' => true]);
    }

    public function down(): void
    {
        // Visibility is an editorial decision; do not hide reviews on rollback.
    }
};
