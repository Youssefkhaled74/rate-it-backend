<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('place_id');
            $table->string('name')->nullable();
            $table->text('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->json('working_hours')->nullable();
            $table->string('qr_code_value')->unique();
            $table->timestampTz('qr_generated_at')->nullable();
            $table->integer('review_cooldown_days')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('place_id');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
