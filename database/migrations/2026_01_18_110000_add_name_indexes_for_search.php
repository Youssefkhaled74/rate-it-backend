<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('brands') && !Schema::hasColumn('brands', 'name')) {
            return;
        }
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (! $this->indexExists('brands', 'brands_name_index')) {
                    $table->index('name', 'brands_name_index');
                }
            });
        }

        if (Schema::hasTable('places')) {
            Schema::table('places', function (Blueprint $table) {
                if (! $this->indexExists('places', 'places_name_index')) {
                    $table->index('name', 'places_name_index');
                }
            });
        }

        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                if (! $this->indexExists('branches', 'branches_name_index')) {
                    $table->index('name', 'branches_name_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if ($this->indexExists('brands', 'brands_name_index')) {
                    $table->dropIndex('brands_name_index');
                }
            });
        }

        if (Schema::hasTable('places')) {
            Schema::table('places', function (Blueprint $table) {
                if ($this->indexExists('places', 'places_name_index')) {
                    $table->dropIndex('places_name_index');
                }
            });
        }

        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                if ($this->indexExists('branches', 'branches_name_index')) {
                    $table->dropIndex('branches_name_index');
                }
            });
        }
    }

    protected function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$database, $table, $index]
        );
        return $row && ($row->cnt ?? 0) > 0;
    }
};
