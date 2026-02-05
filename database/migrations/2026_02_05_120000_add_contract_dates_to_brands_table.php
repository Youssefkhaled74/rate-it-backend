<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'start_date')) {
                $table->date('start_date')->nullable()->after('points_expiry_days');
            }
            if (!Schema::hasColumn('brands', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('brands', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
