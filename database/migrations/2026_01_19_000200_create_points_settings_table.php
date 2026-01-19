<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('points_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('points_per_review')->default(0);
            $table->integer('invite_points_per_friend')->default(50);
            $table->integer('invitee_bonus_points')->default(0);
            $table->decimal('point_value_money', 10, 2)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active','created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('points_settings');
    }
}
