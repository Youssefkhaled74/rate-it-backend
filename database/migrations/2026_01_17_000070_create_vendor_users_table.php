<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVendorUsersTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password_hash')->nullable();
            $table->enum('role', ['VENDOR_ADMIN','BRANCH_STAFF'])->default('VENDOR_ADMIN');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('brand_id');
            $table->index('branch_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add check constraint: BRANCH_STAFF must have branch_id not null
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE vendor_users ADD CONSTRAINT vendor_users_branch_required CHECK (role != 'BRANCH_STAFF' OR branch_id IS NOT NULL);");
        }
    }

    public function down()
    {
        Schema::dropIfExists('vendor_users');
    }
}
