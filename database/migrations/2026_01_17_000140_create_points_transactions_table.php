<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePointsTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->enum('type', ['EARN_REVIEW','REDEEM_VOUCHER','ADJUST_ADMIN','EXPIRE']);
            $table->integer('points');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','created_at']);
            $table->index(['user_id','expires_at']);
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('points_transactions');
    }
}
