<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min_reviews')->default(0);
            $table->json('benefits')->nullable();
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_levels');
    }
}
