<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchQrSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_qr_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('qr_code_value');
            $table->string('session_token')->unique();
            $table->timestampTz('scanned_at');
            $table->timestampTz('expires_at');
            $table->timestampTz('consumed_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','branch_id']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_qr_sessions');
    }
}
