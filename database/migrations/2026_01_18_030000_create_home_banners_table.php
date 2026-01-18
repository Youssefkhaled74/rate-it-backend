<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeBannersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('home_banners')) {
            return;
        }

        Schema::create('home_banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('body_en')->nullable();
            $table->text('body_ar')->nullable();
            $table->string('image');
            $table->string('action_type')->nullable();
            $table->string('action_value')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_banners');
    }
}
