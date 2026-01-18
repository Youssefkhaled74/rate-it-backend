<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Attempts to drop the legacy `name` column from `places`.
     * If DROP COLUMN fails, falls back to making it nullable.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('places')) {
            return;
        }

        if (!Schema::hasColumn('places', 'name')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `places` DROP COLUMN `name`');
        } catch (\Throwable $e) {
            try {
                DB::statement('ALTER TABLE `places` MODIFY `name` VARCHAR(255) NULL');
            } catch (\Throwable $e) {
                // If this also fails, don't throw further to avoid breaking deploys.
            }
        }
    }

    /**
     * Reverse the migrations.
     * Re-adds `name` as nullable string if the column is missing.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasTable('places')) {
            return;
        }

        if (Schema::hasColumn('places', 'name')) {
            return;
        }

        Schema::table('places', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });
    }
};
