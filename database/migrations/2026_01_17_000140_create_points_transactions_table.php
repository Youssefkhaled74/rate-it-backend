<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('brand_id')->nullable();
            $table->enum('type', ['EARN_REVIEW','REDEEM_VOUCHER','ADJUST_ADMIN','EXPIRE']);
            $table->integer('points');
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','created_at']);
            $table->index(['user_id','expires_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('points_transactions');
    }
}
