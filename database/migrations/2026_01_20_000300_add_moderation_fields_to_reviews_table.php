<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModerationFieldsToReviewsTable extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('status');
            $table->text('hidden_reason')->nullable()->after('is_hidden');
            $table->timestampTz('hidden_at')->nullable()->after('hidden_reason');
            $table->foreignId('hidden_by_admin_id')->nullable()->after('hidden_at')->constrained('admins')->nullOnDelete();

            $table->text('admin_reply_text')->nullable()->after('hidden_by_admin_id');
            $table->timestampTz('replied_at')->nullable()->after('admin_reply_text');
            $table->foreignId('replied_by_admin_id')->nullable()->after('replied_at')->constrained('admins')->nullOnDelete();

            $table->boolean('is_featured')->default(false)->after('replied_by_admin_id');
            $table->timestampTz('featured_at')->nullable()->after('is_featured');
            $table->foreignId('featured_by_admin_id')->nullable()->after('featured_at')->constrained('admins')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'featured_by_admin_id')) {
                $table->dropForeign(['featured_by_admin_id']);
            }
            if (Schema::hasColumn('reviews', 'replied_by_admin_id')) {
                $table->dropForeign(['replied_by_admin_id']);
            }
            if (Schema::hasColumn('reviews', 'hidden_by_admin_id')) {
                $table->dropForeign(['hidden_by_admin_id']);
            }

            $table->dropColumn([
                'is_hidden','hidden_reason','hidden_at','hidden_by_admin_id',
                'admin_reply_text','replied_at','replied_by_admin_id',
                'is_featured','featured_at','featured_by_admin_id'
            ]);
        });
    }
}
