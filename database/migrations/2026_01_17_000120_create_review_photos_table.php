<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewPhotosTable extends Migration
{
    public function up()
    {
        Schema::create('review_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->string('storage_path');
            $table->boolean('encrypted')->default(true);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('review_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_photos');
    }
}
