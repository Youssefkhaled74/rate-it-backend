<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->integer('amount_cents')->default(0);
            $table->string('currency', 6)->default('USD');
            $table->string('status')->default('pending');
            $table->string('provider')->nullable();
            $table->string('provider_txn_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_transactions');
    }
}
