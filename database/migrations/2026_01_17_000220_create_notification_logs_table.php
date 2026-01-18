<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationLogsTable extends Migration
{
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel');
            $table->string('target_table');
            $table->uuid('target_id');
            $table->string('type');
            $table->json('payload')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs');
    }
}
