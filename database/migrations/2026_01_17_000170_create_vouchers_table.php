<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('brand_id');
            $table->string('code')->unique();
            $table->integer('points_used');
            $table->decimal('value_amount', 10, 2)->nullable();
            $table->enum('status', ['VALID','USED','EXPIRED'])->default('VALID');
            $table->timestampTz('issued_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('used_at')->nullable();
            $table->uuid('used_branch_id')->nullable();
            $table->uuid('verified_by_vendor_user_id')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','status']);
            $table->index(['brand_id','status']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('used_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('verified_by_vendor_user_id')->references('id')->on('vendor_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
