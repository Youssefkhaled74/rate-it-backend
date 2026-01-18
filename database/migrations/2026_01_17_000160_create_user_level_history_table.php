<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLevelHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('user_level_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('level_id');
            $table->timestampTz('achieved_at')->nullable();
            $table->timestampsTz();

            $table->unique(['user_id','level_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('user_levels')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_level_history');
    }
}
