<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subcategories')) {
            return;
        }

        Schema::table('subcategories', function (Blueprint $table) {
            if (! Schema::hasColumn('subcategories', 'image')) {
                $table->string('image')->nullable()->after('name_ar');
            }

            if (! Schema::hasColumn('subcategories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image');
            }

            // safe index for queries by category and active flag using INFORMATION_SCHEMA
            $database = DB::getDatabaseName();
            $row = DB::selectOne(
                'SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
                [$database, 'subcategories', 'subcategories_category_id_is_active_index']
            );

            if (! ($row && ($row->cnt ?? 0) > 0)) {
                $table->index(['category_id', 'is_active'], 'subcategories_category_id_is_active_index');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subcategories')) {
            return;
        }

        Schema::table('subcategories', function (Blueprint $table) {
            if (Schema::hasColumn('subcategories', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('subcategories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            $database = DB::getDatabaseName();
            $row = DB::selectOne(
                'SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
                [$database, 'subcategories', 'subcategories_category_id_is_active_index']
            );

            if ($row && ($row->cnt ?? 0) > 0) {
                try {
                    $table->dropIndex('subcategories_category_id_is_active_index');
                } catch (\Throwable $e) {
                    // ignore if cannot drop
                }
            }
        });
    }
};
