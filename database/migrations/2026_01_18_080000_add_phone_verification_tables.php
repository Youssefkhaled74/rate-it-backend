<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneVerificationTables extends Migration
{
    public function up()
    {
        // add phone_verified_at to users
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'phone_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone');
            });
        }

        // create phone_verification_tokens table
        if (! Schema::hasTable('phone_verification_tokens')) {
            Schema::create('phone_verification_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('phone')->index();
                $table->string('otp_hash');
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('consumed_at')->nullable();
                $table->integer('attempt_count')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('phone_verification_tokens')) {
            Schema::dropIfExists('phone_verification_tokens');
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phone_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone_verified_at');
            });
        }
    }
}
