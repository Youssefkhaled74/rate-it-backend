<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('places')) {
            return;
        }

        Schema::table('places', function (Blueprint $table) {
            if (! Schema::hasColumn('places', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (! Schema::hasColumn('places', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (! Schema::hasColumn('places', 'logo')) {
                $table->string('logo')->nullable()->after('description');
            }
            if (! Schema::hasColumn('places', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('logo');
            }
            if (! Schema::hasColumn('places', 'description_en')) {
                $table->text('description_en')->nullable()->after('cover_image');
            }
            if (! Schema::hasColumn('places', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description_en');
            }
            if (! Schema::hasColumn('places', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description_ar');
            }
        });

        try {
            DB::table('places')->whereNotNull('name')->whereNull('name_en')->update(['name_en' => DB::raw('name')]);
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::table('places')->whereNotNull('description')->whereNull('description_en')->update(['description_en' => DB::raw('description')]);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('places')) {
            return;
        }

        Schema::table('places', function (Blueprint $table) {
            if (Schema::hasColumn('places', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('places', 'description_ar')) {
                $table->dropColumn('description_ar');
            }
            if (Schema::hasColumn('places', 'description_en')) {
                $table->dropColumn('description_en');
            }
            if (Schema::hasColumn('places', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
            if (Schema::hasColumn('places', 'logo')) {
                $table->dropColumn('logo');
            }
            if (Schema::hasColumn('places', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('places', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });
    }
};
