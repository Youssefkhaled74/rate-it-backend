<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalizationToRatingCriteria extends Migration
{
    public function up()
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (!Schema::hasColumn('rating_criteria', 'question_en')) {
                $table->text('question_en')->nullable()->after('question_text');
            }
            if (!Schema::hasColumn('rating_criteria', 'question_ar')) {
                $table->text('question_ar')->nullable()->after('question_en');
            }
        });
    }

    public function down()
    {
        Schema::table('rating_criteria', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria', 'question_en')) {
                $table->dropColumn('question_en');
            }
            if (Schema::hasColumn('rating_criteria', 'question_ar')) {
                $table->dropColumn('question_ar');
            }
        });
    }
}
