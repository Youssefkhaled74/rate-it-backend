<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingCriteriaTable extends Migration
{
    public function up()
    {
        Schema::create('rating_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('type', ['RATING','YES_NO','MULTIPLE_CHOICE']);
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['subcategory_id','sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rating_criteria');
    }
}
