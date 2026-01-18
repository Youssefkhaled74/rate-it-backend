<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLevelHistoryTable extends Migration
{
    public function up()
    {
        $userIdIsUuid = false;
        try {
            $col = DB::selectOne("SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", ['users','id']);
            if ($col && (stripos($col->COLUMN_TYPE, 'char') !== false || in_array(strtolower($col->DATA_TYPE), ['char','varchar']))) {
                $userIdIsUuid = true;
            }
        } catch (\Exception $e) {
            $userIdIsUuid = true;
        }

        Schema::create('user_level_history', function (Blueprint $table) use ($userIdIsUuid) {
            $table->uuid('id')->primary();
            if ($userIdIsUuid) {
                $table->uuid('user_id');
            } else {
                $table->unsignedBigInteger('user_id');
            }
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
