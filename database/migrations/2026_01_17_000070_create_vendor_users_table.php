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
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
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
        });

        // Add check constraint: BRANCH_STAFF must have branch_id not null
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE vendor_users ADD CONSTRAINT vendor_users_branch_required CHECK (role != 'BRANCH_STAFF' OR branch_id IS NOT NULL);");
        }
    }

    public function down()
    {
        // Drop postgresql-only check constraint if exists
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE vendor_users DROP CONSTRAINT IF EXISTS vendor_users_branch_required');
        }

        Schema::dropIfExists('vendor_users');
    }
}
