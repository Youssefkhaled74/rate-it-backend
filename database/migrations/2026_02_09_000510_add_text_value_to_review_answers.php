<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('review_answers', 'text_value')) {
                $table->text('text_value')->nullable()->after('choice_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('review_answers', function (Blueprint $table) {
            if (Schema::hasColumn('review_answers', 'text_value')) {
                $table->dropColumn('text_value');
            }
        });
    }
};
