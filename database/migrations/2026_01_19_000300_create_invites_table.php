<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inviter_user_id');
            $table->string('invited_phone')->index();
            $table->unsignedBigInteger('invited_user_id')->nullable();
            $table->enum('status', ['pending','joined','rejected','expired'])->default('pending');
            $table->integer('reward_points')->default(50);
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->foreign('inviter_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invited_user_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['inviter_user_id','invited_phone']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invites');
    }
};
