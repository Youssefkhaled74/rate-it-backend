<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['FREE','ACTIVE','EXPIRED'])->default('FREE');
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('free_until')->nullable();
            $table->timestampTz('paid_until')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
