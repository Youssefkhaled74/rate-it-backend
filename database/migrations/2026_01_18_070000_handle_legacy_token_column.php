<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HandleLegacyTokenColumn extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        // If old `token` exists and `token_hash` does not, attempt to rename it.
        if (Schema::hasColumn('password_reset_tokens', 'token') && ! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
            try {
                DB::statement('ALTER TABLE `password_reset_tokens` CHANGE `token` `token_hash` VARCHAR(255)');
            } catch (\Exception $e) {
                // If rename fails, make `token` nullable so inserts that omit it will succeed,
                // then create `token_hash` and copy values over.
                try {
                    DB::statement('ALTER TABLE `password_reset_tokens` MODIFY `token` VARCHAR(255) NULL');
                } catch (\Exception $e2) {
                    // ignore
                }

                // Add token_hash column if missing
                Schema::table('password_reset_tokens', function (Blueprint $table) {
                    if (! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                        $table->string('token_hash')->nullable();
                    }
                });

                // Copy token -> token_hash where token_hash is NULL
                try {
                    DB::statement('UPDATE `password_reset_tokens` SET `token_hash` = `token` WHERE `token_hash` IS NULL');
                } catch (\Exception $e3) {
                    // ignore copy failures
                }
            }
        }

        // If both columns exist, ensure token is nullable so our inserts won't fail
        if (Schema::hasColumn('password_reset_tokens', 'token')) {
            try {
                DB::statement('ALTER TABLE `password_reset_tokens` MODIFY `token` VARCHAR(255) NULL');
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Ensure token_hash exists (final safety)
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                $table->string('token_hash')->nullable();
            }
        });
    }

    public function down()
    {
        // Non-destructive down: do not drop or rename columns to avoid data loss.
    }
}
