<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserNotificationsTable extends Migration
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

        Schema::create('user_notifications', function (Blueprint $table) use ($userIdIsUuid) {
            $table->uuid('id')->primary();
            if ($userIdIsUuid) {
                $table->uuid('user_id');
            } else {
                $table->unsignedBigInteger('user_id');
            }
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestampTz('sent_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','is_read']);
            $table->index(['user_id','created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
}
