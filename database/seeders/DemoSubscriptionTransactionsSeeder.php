<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSubscriptionTransactionsSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.subscription_transactions', 200);
        $users = DB::table('users')->pluck('id')->toArray();
        $plans = DB::table('subscription_plans')->pluck('id')->toArray();
        $subscriptions = DB::table('subscriptions')->pluck('id')->toArray();

        if (empty($users) || empty($plans) || empty($subscriptions)) {
            return;
        }

        $statuses = ['paid', 'success', 'completed', 'pending', 'failed'];
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $userId = $users[array_rand($users)];
            $planId = $plans[array_rand($plans)];
            $subscriptionId = $subscriptions[array_rand($subscriptions)];
            $status = $statuses[array_rand($statuses)];
            $amount = rand(5000, 20000);
            $createdAt = now()->subDays(rand(0, 90))->subMinutes(rand(0, 1440));

            $rows[] = [
                'user_id' => $userId,
                'subscription_id' => $subscriptionId,
                'plan_id' => $planId,
                'amount_cents' => $amount,
                'currency' => 'USD',
                'status' => $status,
                'provider' => 'manual',
                'provider_txn_id' => 'txn_'.$i.'_'.rand(1000, 9999),
                'meta' => json_encode(['source' => 'demo']),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        DB::table('subscription_transactions')->insert($rows);

        // Attach plan id to subscriptions for better reporting/UI
        DB::table('subscriptions')
            ->whereNull('subscription_plan_id')
            ->update(['subscription_plan_id' => $plans[array_rand($plans)]]);
    }
}
