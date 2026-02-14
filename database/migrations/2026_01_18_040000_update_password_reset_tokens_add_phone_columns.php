<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdatePasswordResetTokensAddPhoneColumns extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('password_reset_tokens');
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('phone')->nullable()->index();
                $table->string('token_hash')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });

            return;
        }

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
        // If a legacy 'email' primary column exists (from older migrations), remove it
        if (Schema::hasColumn('password_reset_tokens', 'email')) {
            // Drop primary key if it's set on email
            try {
                DB::statement('ALTER TABLE password_reset_tokens DROP PRIMARY KEY');
            } catch (\Exception $e) {
                // ignore if cannot drop primary (DB engine may vary)
            }

            // Now drop the legacy email column
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                if (Schema::hasColumn('password_reset_tokens', 'email')) {
                    $table->dropColumn('email');
                }
            });
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'phone')) {
                if (Schema::hasColumn('password_reset_tokens', 'email')) {
                    $table->string('phone')->nullable()->index()->after('email');
                } else {
                    $table->string('phone')->nullable()->index();
                }
            }
            if (! Schema::hasColumn('password_reset_tokens', 'token_hash')) {
                // place token_hash after phone if phone exists, otherwise append
                if (Schema::hasColumn('password_reset_tokens', 'phone')) {
                    $table->string('token_hash')->nullable()->after('phone');
                } else {
                    $table->string('token_hash')->nullable();
                }
            }
            if (! Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->index();
            }

            // Add created_at/updated_at individually to avoid duplicate column errors
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
