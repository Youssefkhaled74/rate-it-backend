<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('is_active');
            $table->text('blocked_reason')->nullable()->after('is_blocked');
            $table->timestampTz('blocked_at')->nullable()->after('blocked_reason');
            $table->foreignId('blocked_by_admin_id')->nullable()->after('blocked_at')->constrained('admins')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'blocked_by_admin_id')) {
                $table->dropForeign(['blocked_by_admin_id']);
            }
            $table->dropColumn(['is_blocked','blocked_reason','blocked_at','blocked_by_admin_id']);
        });
    }
}
