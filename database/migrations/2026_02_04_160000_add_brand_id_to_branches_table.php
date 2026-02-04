<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('place_id')->constrained('brands')->nullOnDelete();
            $table->index('brand_id');
        });

        // Backfill brand_id from places
        DB::statement('
            UPDATE branches b
            JOIN places p ON p.id = b.place_id
            SET b.brand_id = p.brand_id
            WHERE b.brand_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(["brand_id"]);
            $table->dropIndex(["brand_id"]);
            $table->dropColumn("brand_id");
        });
    }
};
