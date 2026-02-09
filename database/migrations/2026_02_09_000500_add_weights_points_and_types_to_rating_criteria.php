<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (! Schema::hasColumn('rating_criteria', 'weight')) {
                $table->decimal('weight', 5, 2)->default(1)->after('type');
            }
            if (! Schema::hasColumn('rating_criteria', 'points')) {
                $table->integer('points')->default(0)->after('weight');
            }
        });

        // Expand enum to include TEXT and PHOTO (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rating_criteria MODIFY COLUMN type ENUM('RATING','YES_NO','MULTIPLE_CHOICE','TEXT','PHOTO') NOT NULL");
        }
    }

    public function down(): void
    {
        // Revert enum (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rating_criteria MODIFY COLUMN type ENUM('RATING','YES_NO','MULTIPLE_CHOICE') NOT NULL");
        }

        Schema::table('rating_criteria', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria', 'points')) {
                $table->dropColumn('points');
            }
            if (Schema::hasColumn('rating_criteria', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
