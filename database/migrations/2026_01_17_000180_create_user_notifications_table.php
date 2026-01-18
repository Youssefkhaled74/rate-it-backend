<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestampTz('sent_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','is_read']);
            $table->index(['user_id','created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
}
