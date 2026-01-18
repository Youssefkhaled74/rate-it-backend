<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            if (! Schema::hasColumn('brands', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (! Schema::hasColumn('brands', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (! Schema::hasColumn('brands', 'logo')) {
                $table->string('logo')->nullable()->after('logo_url');
            }
            if (! Schema::hasColumn('brands', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('logo');
            }
            if (! Schema::hasColumn('brands', 'description_en')) {
                $table->text('description_en')->nullable()->after('cover_image');
            }
            if (! Schema::hasColumn('brands', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description_en');
            }
            if (! Schema::hasColumn('brands', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description_ar');
            }
        });

        // copy existing name/logo_url into new columns if present
        try {
            DB::table('brands')->whereNotNull('name')->whereNull('name_en')->update(['name_en' => DB::raw('name')]);
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::table('brands')->whereNotNull('logo_url')->whereNull('logo')->update(['logo' => DB::raw('logo_url')]);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('brands', 'description_ar')) {
                $table->dropColumn('description_ar');
            }
            if (Schema::hasColumn('brands', 'description_en')) {
                $table->dropColumn('description_en');
            }
            if (Schema::hasColumn('brands', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
            if (Schema::hasColumn('brands', 'logo')) {
                $table->dropColumn('logo');
            }
            if (Schema::hasColumn('brands', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('brands', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });
    }
};
