<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'logo')) {
                $table->string('logo')->nullable()->after('name');
            }
            if (!Schema::hasColumn('branches', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
            if (Schema::hasColumn('branches', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
