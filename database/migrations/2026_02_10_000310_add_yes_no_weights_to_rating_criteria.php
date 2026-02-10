<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (! Schema::hasColumn('rating_criteria', 'yes_value')) {
                $table->integer('yes_value')->default(5)->after('points');
            }
            if (! Schema::hasColumn('rating_criteria', 'no_value')) {
                $table->integer('no_value')->default(1)->after('yes_value');
            }
            if (! Schema::hasColumn('rating_criteria', 'yes_weight')) {
                $table->float('yes_weight')->default(1)->after('no_value');
            }
            if (! Schema::hasColumn('rating_criteria', 'no_weight')) {
                $table->float('no_weight')->default(1)->after('yes_weight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria', 'yes_value')) {
                $table->dropColumn('yes_value');
            }
            if (Schema::hasColumn('rating_criteria', 'no_value')) {
                $table->dropColumn('no_value');
            }
            if (Schema::hasColumn('rating_criteria', 'yes_weight')) {
                $table->dropColumn('yes_weight');
            }
            if (Schema::hasColumn('rating_criteria', 'no_weight')) {
                $table->dropColumn('no_weight');
            }
        });
    }
};
