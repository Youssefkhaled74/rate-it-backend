<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('place_id')->nullable()->constrained('places')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
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
            $table->foreign('place_id')->references('id')->on('places')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
