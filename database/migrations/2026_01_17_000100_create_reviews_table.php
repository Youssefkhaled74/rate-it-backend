<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        // Ensure user_id column type matches users.id (uuid vs int)
        $userIdIsUuid = false;
        try {
            $col = DB::selectOne("SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", ['users','id']);
            if ($col && (stripos($col->COLUMN_TYPE, 'char') !== false || in_array(strtolower($col->DATA_TYPE), ['char','varchar']))) {
                $userIdIsUuid = true;
            }
        } catch (\Exception $e) {
            // default to uuid
            $userIdIsUuid = true;
        }

        Schema::create('reviews', function (Blueprint $table) use ($userIdIsUuid) {
            $table->uuid('id')->primary();
            if ($userIdIsUuid) {
                $table->uuid('user_id');
            } else {
                $table->unsignedBigInteger('user_id');
            }
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
            if ($userIdIsUuid) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } else {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            $table->foreign('place_id')->references('id')->on('places')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
