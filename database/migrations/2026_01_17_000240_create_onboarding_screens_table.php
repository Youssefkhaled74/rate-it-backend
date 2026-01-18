<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnboardingScreensTable extends Migration
{
    public function up()
    {
        Schema::create('onboarding_screens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('onboarding_screens');
    }
}
