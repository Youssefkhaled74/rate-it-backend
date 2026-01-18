<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('review_answers', function (Blueprint $table) {
              $table->id();
              $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
              $table->foreignId('criteria_id')->constrained('rating_criteria')->cascadeOnDelete();
            $table->smallInteger('rating_value')->nullable();
            $table->boolean('yes_no_value')->nullable();
              $table->foreignId('choice_id')->nullable()->constrained('rating_criteria_choices')->nullOnDelete();
            $table->timestampsTz();

            $table->unique(['review_id','criteria_id']);
            $table->index('review_id');
            $table->index('criteria_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_answers');
    }
}
