<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToRatingCriteriaTable extends Migration
{
    public function up()
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (!Schema::hasColumn('rating_criteria', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_required');
            }
        });
    }

    public function down()
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
}
