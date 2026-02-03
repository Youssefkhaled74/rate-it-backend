<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            }
            if (!Schema::hasColumn('branches', 'area_id')) {
                $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'area_id')) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            }
            if (Schema::hasColumn('branches', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
        });
    }
};
