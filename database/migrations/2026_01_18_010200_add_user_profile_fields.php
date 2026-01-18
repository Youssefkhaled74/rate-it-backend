<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProfileFields extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'gender_id')) {
                $table->foreignId('gender_id')->nullable()->constrained('genders')->onDelete('restrict')->after('birth_date');
            }
            if (!Schema::hasColumn('users', 'nationality_id')) {
                $table->foreignId('nationality_id')->nullable()->constrained('nationalities')->onDelete('restrict')->after('gender_id');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nationality_id')) {
                $table->dropConstrainedForeignId('nationality_id');
            }
            if (Schema::hasColumn('users', 'gender_id')) {
                $table->dropConstrainedForeignId('gender_id');
            }
            if (Schema::hasColumn('users', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }
}
