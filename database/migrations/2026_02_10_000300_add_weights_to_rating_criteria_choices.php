<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (! Schema::hasColumn('rating_criteria_choices', 'weight')) {
                $table->float('weight')->default(1)->after('value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rating_criteria_choices', function (Blueprint $table) {
            if (Schema::hasColumn('rating_criteria_choices', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
