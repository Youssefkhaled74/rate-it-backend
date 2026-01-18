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
            $table->id();
            $table->enum('actor_type', ['USER','ADMIN','VENDOR_USER']);
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('actor_vendor_user_id')->nullable()->constrained('vendor_users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
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
        // Drop postgresql-only check constraint if exists
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE activity_logs DROP CONSTRAINT IF EXISTS activity_actor_one_not_null');
        }

        Schema::dropIfExists('activity_logs');
    }
}
