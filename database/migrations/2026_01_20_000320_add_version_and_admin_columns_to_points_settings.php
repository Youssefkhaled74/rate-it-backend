<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (! class_exists('AddVersionAndAdminColumnsToPointsSettingsTable20260120')) {
class AddVersionAndAdminColumnsToPointsSettingsTable20260120 extends Migration
{
    public function up()
    {
        Schema::table('points_settings', function (Blueprint $table) {
            $table->unsignedInteger('version')->nullable()->after('id');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete()->after('currency');
            $table->foreignId('activated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete()->after('created_by_admin_id');
            $table->timestamp('activated_at')->nullable()->after('activated_by_admin_id');
        });

        // assign incremental versions based on created_at ordering
        $rows = DB::table('points_settings')->orderBy('created_at')->get();
        $v = 1;
        foreach ($rows as $r) {
            DB::table('points_settings')->where('id', $r->id)->update(['version' => $v++]);
        }

        // enforce uniqueness
        Schema::table('points_settings', function (Blueprint $table) {
            $table->unique('version');
        });
    }

    public function down()
    {
        Schema::table('points_settings', function (Blueprint $table) {
            $table->dropUnique(['version']);
            $table->dropColumn(['version','created_by_admin_id','activated_by_admin_id','activated_at']);
        });
    }
}
}

// Laravel's migrator derives class names from file names (AddVersionAndAdminColumnsToPointsSettings).
// Create a small alias class so the migrator can instantiate it safely.
if (! class_exists('AddVersionAndAdminColumnsToPointsSettings')) {
    class AddVersionAndAdminColumnsToPointsSettings extends AddVersionAndAdminColumnsToPointsSettingsTable20260120 {}
}
