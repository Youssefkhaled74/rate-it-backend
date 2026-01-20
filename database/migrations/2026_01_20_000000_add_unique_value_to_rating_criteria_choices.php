<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueValueToRatingCriteriaChoices extends Migration
{
    public function up()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            // add unique index on (criteria_id, value) if not exists
            $table->unique(['criteria_id', 'value'], 'rating_criteria_choices_criteria_value_unique');
        });
    }

    public function down()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            $table->dropUnique('rating_criteria_choices_criteria_value_unique');
        });
    }
}
