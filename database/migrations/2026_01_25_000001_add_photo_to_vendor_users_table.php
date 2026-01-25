<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToVendorUsersTable extends Migration
{
    public function up()
    {
        Schema::table('vendor_users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('password_hash');
        });
    }

    public function down()
    {
        Schema::table('vendor_users', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
}
