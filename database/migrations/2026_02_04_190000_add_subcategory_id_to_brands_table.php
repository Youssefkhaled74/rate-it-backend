<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('brands')) return;

        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'subcategory_id')) {
                $table->foreignId('subcategory_id')->nullable()->after('cover_image')->constrained('subcategories')->nullOnDelete();
                $table->index('subcategory_id');
            }
        });

        // Backfill from places: pick first non-null subcategory_id per brand
        if (Schema::hasTable('places') && Schema::hasColumn('places', 'subcategory_id')) {
            $rows = DB::table('places')
                ->select('brand_id', DB::raw('MIN(subcategory_id) as subcategory_id'))
                ->whereNotNull('brand_id')
                ->whereNotNull('subcategory_id')
                ->groupBy('brand_id')
                ->get();

            foreach ($rows as $row) {
                DB::table('brands')
                    ->where('id', $row->brand_id)
                    ->whereNull('subcategory_id')
                    ->update(['subcategory_id' => $row->subcategory_id]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('brands')) return;

        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'subcategory_id')) {
                $table->dropIndex(['subcategory_id']);
                $table->dropConstrainedForeignId('subcategory_id');
            }
        });
    }
};
