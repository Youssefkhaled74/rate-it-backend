<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('actor_type', ['USER','ADMIN','VENDOR_USER']);
            $table->uuid('actor_user_id')->nullable();
            $table->uuid('actor_admin_id')->nullable();
            $table->uuid('actor_vendor_user_id')->nullable();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->uuid('entity_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['actor_type','created_at']);
            $table->index(['entity_type','entity_id']);
            $table->index('actor_user_id');
            $table->index('actor_admin_id');
            $table->index('actor_vendor_user_id');
        });

        // Enforce exactly one actor id is not null (Postgres only)
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE activity_logs ADD CONSTRAINT activity_actor_one_not_null CHECK ( (CASE WHEN actor_user_id IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN actor_admin_id IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN actor_vendor_user_id IS NOT NULL THEN 1 ELSE 0 END) = 1 );");
        }
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
