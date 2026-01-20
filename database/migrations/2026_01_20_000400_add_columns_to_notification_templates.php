<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToNotificationTemplates extends Migration
{
    public function up()
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('notification_templates', 'key')) {
                $table->string('key')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('notification_templates', 'title_en')) {
                $table->string('title_en')->nullable()->after('title_tpl');
            }
            if (! Schema::hasColumn('notification_templates', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title_en');
            }
            if (! Schema::hasColumn('notification_templates', 'body_en')) {
                $table->text('body_en')->nullable()->after('body_tpl');
            }
            if (! Schema::hasColumn('notification_templates', 'body_ar')) {
                $table->text('body_ar')->nullable()->after('body_en');
            }
            if (! Schema::hasColumn('notification_templates', 'variables_schema')) {
                $table->json('variables_schema')->nullable()->after('body_ar');
            }
            if (! Schema::hasColumn('notification_templates', 'created_by_admin_id')) {
                $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('notification_templates', 'updated_by_admin_id')) {
                $table->unsignedBigInteger('updated_by_admin_id')->nullable()->after('created_by_admin_id');
            }
        });
    }

    public function down()
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            foreach (['key','title_en','title_ar','body_en','body_ar','variables_schema','created_by_admin_id','updated_by_admin_id'] as $c) {
                if (Schema::hasColumn('notification_templates', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
}
