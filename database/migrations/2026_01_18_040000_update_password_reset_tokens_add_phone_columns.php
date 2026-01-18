<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePasswordResetTokensAddPhoneColumns extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('phone')->nullable()->index();
                $table->string('token_hash');
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->string('phone')->nullable()->index()->after('email');
            }
            if (! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                // keep old 'token' column for compatibility if exists
                $table->string('token_hash')->nullable()->after('email');
            }
            if (! Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('token_hash')->index();
            }

            // Add created_at/updated_at individually to avoid duplicate column errors
            if (! Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('expires_at');
            }
            if (! Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                $table->dropColumn('token_hash');
            }
            if (Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
}
