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

        $hasIndex = false;
        if (DB::getDriverName() === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('subcategories')");
            foreach ($rows as $row) {
                if (($row->name ?? null) === 'subcategories_category_id_is_active_index') {
                    $hasIndex = true;
                    break;
                }
            }
        } else {
            $database = DB::getDatabaseName();
            $row = DB::selectOne(
                'SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
                [$database, 'subcategories', 'subcategories_category_id_is_active_index']
            );
            $hasIndex = (bool) ($row && ($row->cnt ?? 0) > 0);
        }

        Schema::table('subcategories', function (Blueprint $table) use ($hasIndex) {
            if (! Schema::hasColumn('subcategories', 'image')) {
                $table->string('image')->nullable()->after('name_ar');
            }

            if (! Schema::hasColumn('subcategories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image');
            }

            if (! $hasIndex) {
                $table->index(['category_id', 'is_active'], 'subcategories_category_id_is_active_index');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subcategories')) {
            return;
        }

        $hasIndex = false;
        if (DB::getDriverName() === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('subcategories')");
            foreach ($rows as $row) {
                if (($row->name ?? null) === 'subcategories_category_id_is_active_index') {
                    $hasIndex = true;
                    break;
                }
            }
        } else {
            $database = DB::getDatabaseName();
            $row = DB::selectOne(
                'SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
                [$database, 'subcategories', 'subcategories_category_id_is_active_index']
            );
            $hasIndex = (bool) ($row && ($row->cnt ?? 0) > 0);
        }

        Schema::table('subcategories', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasColumn('subcategories', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('subcategories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if ($hasIndex) {
                try {
                    $table->dropIndex('subcategories_category_id_is_active_index');
                } catch (\Throwable $e) {
                    // ignore if cannot drop
                }
            }
        });
    }
};
