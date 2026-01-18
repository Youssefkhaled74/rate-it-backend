<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DemoActivityLogsSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->pluck('id')->toArray();
        $admins = DB::table('admins')->pluck('id')->toArray();
        $vendors = DB::table('vendor_users')->pluck('id')->toArray();

        // create some user actions
        foreach (array_slice($users,0,50) as $u) {
            DB::table('activity_logs')->insert([
                'actor_type' => 'USER',
                'actor_user_id' => $u,
                'actor_admin_id' => null,
                'actor_vendor_user_id' => null,
                'action' => 'LOGIN',
                'entity_type' => 'USERS',
                'entity_id' => $u,
                'ip_address' => null,
                'user_agent' => null,
                'meta' => null,
                'created_at' => now(),
            ]);
        }

        // some admin actions
        foreach (array_slice($admins,0,5) as $a) {
            DB::table('activity_logs')->insert([
                'actor_type' => 'ADMIN',
                'actor_user_id' => null,
                'actor_admin_id' => $a,
                'actor_vendor_user_id' => null,
                'action' => 'DELETE_REVIEW',
                'entity_type' => 'REVIEWS',
                'entity_id' => null,
                'ip_address' => null,
                'user_agent' => null,
                'meta' => json_encode(['reason' => 'policy_violation']),
                'created_at' => now(),
            ]);
        }

        // vendor actions
        foreach (array_slice($vendors,0,30) as $v) {
            DB::table('activity_logs')->insert([
                'actor_type' => 'VENDOR_USER',
                'actor_user_id' => null,
                'actor_admin_id' => null,
                'actor_vendor_user_id' => $v,
                'action' => 'CREATE_BRANCH',
                'entity_type' => 'BRANCHES',
                'entity_id' => null,
                'ip_address' => null,
                'user_agent' => null,
                'meta' => null,
                'created_at' => now(),
            ]);
        }
    }
}
