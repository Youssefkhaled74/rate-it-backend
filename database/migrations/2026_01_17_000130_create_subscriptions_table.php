<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        $userIdIsUuid = false;
        try {
            $col = DB::selectOne("SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", ['users','id']);
            if ($col && (stripos($col->COLUMN_TYPE, 'char') !== false || in_array(strtolower($col->DATA_TYPE), ['char','varchar']))) {
                $userIdIsUuid = true;
            }
        } catch (\Exception $e) {
            $userIdIsUuid = true;
        }

        Schema::create('subscriptions', function (Blueprint $table) use ($userIdIsUuid) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['FREE','ACTIVE','EXPIRED'])->default('FREE');
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('free_until')->nullable();
            $table->timestampTz('paid_until')->nullable();
            $table->timestampsTz();

            $table->index(['user_id','status']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
