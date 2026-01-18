<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id')->nullable();
            $table->uuid('subcategory_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->json('meta')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('brand_id');
            $table->index('subcategory_id');
            $table->index(['city','area']);
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
        });
        // Optionally add a trigram index for name search in PostgreSQL via raw statement
        // DB::statement('CREATE INDEX places_name_trgm_idx ON places USING gin (name gin_trgm_ops);');
    }

    public function down()
    {
        Schema::dropIfExists('places');
    }
}
