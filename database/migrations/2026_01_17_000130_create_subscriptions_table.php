<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('status', ['FREE','ACTIVE','EXPIRED'])->default('FREE');
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('free_until')->nullable();
            $table->timestampTz('paid_until')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','status']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
