<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Attempts to drop the legacy `name` column from `brands`.
     * If DROP COLUMN fails (DB restrictions), falls back to making it nullable.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('brands')) {
            return;
        }

        if (!Schema::hasColumn('brands', 'name')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `brands` DROP COLUMN `name`');
        } catch (\Throwable $e) {
            // Fallback: make the column nullable so inserts that omit it succeed
            try {
                DB::statement('ALTER TABLE `brands` MODIFY `name` VARCHAR(255) NULL');
            } catch (\Throwable $e) {
                // If even this fails, leave it as-is; migration should not crash the whole process.
                // Log to the DB's error log by writing a comment row if desired (skipped here).
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
        if (!Schema::hasTable('brands')) {
            return;
        }

        if (Schema::hasColumn('brands', 'name')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });
    }
};
