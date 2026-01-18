<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateNationalitiesAddCountryCodeFlagCols extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('nationalities')) {
            return;
        }

        // Add country_code if missing
        if (! Schema::hasColumn('nationalities', 'country_code')) {
            Schema::table('nationalities', function (Blueprint $table) {
                $table->string('country_code', 2)->nullable()->after('id');
            });

            // If an old iso_code column exists, copy values to country_code
            if (Schema::hasColumn('nationalities', 'iso_code')) {
                DB::statement('UPDATE nationalities SET country_code = UPPER(iso_code) WHERE country_code IS NULL');
            }

            // Add unique index for country_code
            Schema::table('nationalities', function (Blueprint $table) {
                $table->unique('country_code');
                $table->index('country_code');
            });
        }

        // Add flag_style if missing
        if (! Schema::hasColumn('nationalities', 'flag_style')) {
            Schema::table('nationalities', function (Blueprint $table) {
                $table->string('flag_style')->default('shiny')->after('name_ar');
            });
        }

        // Add flag_size if missing
        if (! Schema::hasColumn('nationalities', 'flag_size')) {
            Schema::table('nationalities', function (Blueprint $table) {
                $table->unsignedSmallInteger('flag_size')->default(64)->after('flag_style');
            });
        }
    }

    public function down()
    {
        if (! Schema::hasTable('nationalities')) {
            return;
        }

        Schema::table('nationalities', function (Blueprint $table) {
            if (Schema::hasColumn('nationalities', 'flag_size')) {
                $table->dropColumn('flag_size');
            }
            if (Schema::hasColumn('nationalities', 'flag_style')) {
                $table->dropColumn('flag_style');
            }
            if (Schema::hasColumn('nationalities', 'country_code')) {
                $table->dropUnique(['country_code']);
                $table->dropIndex(['country_code']);
                $table->dropColumn('country_code');
            }
        });
    }
}
