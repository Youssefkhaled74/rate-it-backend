<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
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
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
