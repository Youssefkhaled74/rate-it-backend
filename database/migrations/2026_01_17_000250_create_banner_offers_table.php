<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerOffersTable extends Migration
{
    public function up()
    {
        Schema::create('banner_offers', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banner_offers');
    }
}
