<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_user_id');
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestampTz('sent_at')->nullable();
            $table->timestampsTz();

            $table->index(['vendor_user_id','is_read']);
            $table->index(['vendor_user_id','created_at']);
            $table->foreign('vendor_user_id')->references('id')->on('vendor_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_notifications');
    }
}
