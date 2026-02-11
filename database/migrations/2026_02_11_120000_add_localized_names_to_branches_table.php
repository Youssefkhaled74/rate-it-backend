<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (! Schema::hasColumn('branches', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (! Schema::hasColumn('branches', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
        });

        if (Schema::hasColumn('branches', 'name') && Schema::hasColumn('branches', 'name_en')) {
            DB::table('branches')
                ->whereNull('name_en')
                ->whereNotNull('name')
                ->update(['name_en' => DB::raw('name')]);
        }
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('branches', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });
    }
};

