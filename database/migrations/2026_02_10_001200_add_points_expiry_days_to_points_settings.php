<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('points_settings', function (Blueprint $table) {
            $table->integer('points_expiry_days')->nullable()->after('invitee_bonus_points');
        });
    }

    public function down()
    {
        Schema::table('points_settings', function (Blueprint $table) {
            $table->dropColumn('points_expiry_days');
        });
    }
};
