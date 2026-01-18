<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password_hash');
            $table->enum('role', ['SUPER_ADMIN','ADMIN'])->default('ADMIN');
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
