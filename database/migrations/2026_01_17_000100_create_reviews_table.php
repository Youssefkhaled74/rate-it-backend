<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('place_id')->nullable();
            $table->uuid('branch_id');
            $table->decimal('overall_rating', 2, 1)->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['ACTIVE','DELETED_BY_ADMIN'])->default('ACTIVE');
            $table->decimal('review_score', 5, 2)->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('user_id');
            $table->index('branch_id');
            $table->index('place_id');
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
