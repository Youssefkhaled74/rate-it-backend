<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalizationToRatingCriteriaChoices extends Migration
{
    public function up()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (!Schema::hasColumn('rating_criteria_choices', 'choice_en')) {
                $table->string('choice_en')->nullable()->after('choice_text');
            }
            if (!Schema::hasColumn('rating_criteria_choices', 'choice_ar')) {
                $table->string('choice_ar')->nullable()->after('choice_en');
            }
        });
    }

    public function down()
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria_choices', 'choice_en')) {
                $table->dropColumn('choice_en');
            }
            if (Schema::hasColumn('rating_criteria_choices', 'choice_ar')) {
                $table->dropColumn('choice_ar');
            }
        });
    }
}
