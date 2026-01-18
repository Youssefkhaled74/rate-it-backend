<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('review_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('review_id');
            $table->uuid('criteria_id');
            $table->smallInteger('rating_value')->nullable();
            $table->boolean('yes_no_value')->nullable();
            $table->uuid('choice_id')->nullable();
            $table->timestampsTz();

            $table->unique(['review_id','criteria_id']);
            $table->index('review_id');
            $table->index('criteria_id');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('criteria_id')->references('id')->on('rating_criteria')->onDelete('cascade');
            $table->foreign('choice_id')->references('id')->on('rating_criteria_choices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_answers');
    }
}
