<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixPasswordResetTokensForPhone extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            // nothing to fix if table doesn't exist
            return;
        }

        // Make legacy email column nullable (safe option)
        if (Schema::hasColumn('password_reset_tokens', 'email')) {
            try {
                DB::statement('ALTER TABLE `password_reset_tokens` MODIFY `email` VARCHAR(255) NULL');
            } catch (\Exception $e) {
                // ignore failures; best-effort
            }
        }

        // Ensure phone column exists (nullable to avoid blocking existing rows)
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->string('phone')->nullable()->index()->after('email');
            }
        });

        // If an old `token` column exists, rename it to `token_hash` (best-effort)
        if (Schema::hasColumn('password_reset_tokens', 'token') && ! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
            try {
                DB::statement('ALTER TABLE `password_reset_tokens` CHANGE `token` `token_hash` VARCHAR(255)');
            } catch (\Exception $e) {
                // ignore if rename fails
            }
        }

        // Ensure token_hash, expires_at and timestamps exist
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                $table->string('token_hash');
            }
            if (! Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->index();
            }
            if (! Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (! Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                $table->dropColumn('token_hash');
            }
            if (Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });

        // NOTE: we intentionally do not revert email nullability to NOT NULL (destructive)
    }
}