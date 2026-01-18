<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingCriteriaChoicesTable extends Migration
{
    public function up()
    {
        Schema::create('rating_criteria_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')->constrained('rating_criteria')->cascadeOnDelete();
            $table->string('choice_text');
            $table->integer('value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['criteria_id','sort_order']);
            $table->foreign('criteria_id')->references('id')->on('rating_criteria')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rating_criteria_choices');
    }
}
