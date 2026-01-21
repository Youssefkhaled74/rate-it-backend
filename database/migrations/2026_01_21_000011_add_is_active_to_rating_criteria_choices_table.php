<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToRatingCriteriaChoicesTable extends Migration
{
    public function up()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (!Schema::hasColumn('rating_criteria_choices', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
        });
    }

    public function down()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria_choices', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
}
