<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'logo')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('name_ar');
                if (!Schema::hasColumn('categories', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                } else {
                    $table->index('is_active');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'logo')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
    }
};
