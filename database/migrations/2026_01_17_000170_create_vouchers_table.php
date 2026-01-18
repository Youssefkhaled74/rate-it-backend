<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->integer('points_used');
            $table->decimal('value_amount', 10, 2)->nullable();
            $table->enum('status', ['VALID','USED','EXPIRED'])->default('VALID');
            $table->timestampTz('issued_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('used_at')->nullable();
            $table->foreignId('used_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('verified_by_vendor_user_id')->nullable()->constrained('vendor_users')->nullOnDelete();
            $table->timestampsTz();

            $table->index(['user_id','status']);
            $table->index(['brand_id','status']);
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
