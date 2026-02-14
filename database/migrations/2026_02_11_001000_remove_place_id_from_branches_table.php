<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches') || !Schema::hasColumn('branches', 'place_id')) {
            return;
        }

        Schema::table('branches', function (Blueprint $table) {
            try {
                $table->dropForeign(['place_id']);
            } catch (\Throwable $e) {
                // ignore missing foreign key name/state
            }
            try {
                $table->dropIndex(['place_id']);
            } catch (\Throwable $e) {
                // ignore missing index name/state
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('place_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('branches') || Schema::hasColumn('branches', 'place_id')) {
            return;
        }

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('place_id')->nullable()->after('id')->constrained('places')->nullOnDelete();
        });
    }
};
