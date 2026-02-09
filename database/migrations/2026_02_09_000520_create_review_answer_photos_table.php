<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_answer_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_answer_id')->constrained('review_answers')->cascadeOnDelete();
            $table->string('storage_path');
            $table->boolean('encrypted')->default(false);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_answer_photos');
    }
};
