<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNationalitiesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('nationalities')) {
            return;
        }

        Schema::create('nationalities', function (Blueprint $table) {
            $table->id();
            $table->string('iso_code')->nullable()->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nationalities');
    }
}
