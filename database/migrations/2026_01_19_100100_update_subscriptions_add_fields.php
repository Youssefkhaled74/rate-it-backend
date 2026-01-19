<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSubscriptionsAddFields extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'subscription_plan_id')) {
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('subscriptions', 'subscription_status')) {
                $table->string('subscription_status')->default('trialing')->after('status');
            }
            if (!Schema::hasColumn('subscriptions', 'auto_renew')) {
                $table->boolean('auto_renew')->default(true)->after('paid_until');
            }
            if (!Schema::hasColumn('subscriptions', 'canceled_at')) {
                $table->timestampTz('canceled_at')->nullable()->after('auto_renew');
            }
            if (!Schema::hasColumn('subscriptions', 'provider')) {
                $table->string('provider')->nullable()->after('canceled_at');
                $table->string('provider_subscription_id')->nullable()->after('provider');
                $table->string('provider_transaction_id')->nullable()->after('provider_subscription_id');
            }
            if (!Schema::hasColumn('subscriptions', 'meta')) {
                $table->json('meta')->nullable()->after('provider_transaction_id');
            }

            $table->index(['subscription_plan_id']);
            $table->index(['subscription_status']);
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'subscription_plan_id')) {
                $table->dropColumn('subscription_plan_id');
            }
            if (Schema::hasColumn('subscriptions', 'subscription_status')) {
                $table->dropColumn('subscription_status');
            }
            if (Schema::hasColumn('subscriptions', 'auto_renew')) {
                $table->dropColumn('auto_renew');
            }
            if (Schema::hasColumn('subscriptions', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }
            if (Schema::hasColumn('subscriptions', 'provider')) {
                $table->dropColumn('provider');
                $table->dropColumn('provider_subscription_id');
                $table->dropColumn('provider_transaction_id');
            }
            if (Schema::hasColumn('subscriptions', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
}
