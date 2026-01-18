<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserLevelHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('user_level_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('user_levels')->cascadeOnDelete();
            $table->timestampTz('achieved_at')->nullable();
            $table->timestampsTz();

            $table->unique(['user_id','level_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_level_history');
    }
}
