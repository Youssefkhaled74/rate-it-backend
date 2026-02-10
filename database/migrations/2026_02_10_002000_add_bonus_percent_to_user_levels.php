<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_levels', function (Blueprint $table) {
            $table->decimal('bonus_percent', 5, 2)->default(0)->after('min_reviews');
        });
    }

    public function down()
    {
        Schema::table('user_levels', function (Blueprint $table) {
            $table->dropColumn('bonus_percent');
        });
    }
};
