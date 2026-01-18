<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DemoNotificationsSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->pluck('id')->toArray();
        $vendors = DB::table('vendor_users')->pluck('id')->toArray();
        $admins = DB::table('admins')->pluck('id')->toArray();

        // user notifications
        foreach (array_slice($users,0,50) as $u) {
            DB::table('user_notifications')->insert([
                'user_id' => $u,
                'type' => 'WELCOME',
                'title' => 'Welcome',
                'body' => 'Welcome to Rate It',
                'data' => null,
                'is_read' => false,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // vendor notifications
        foreach (array_slice($vendors,0,30) as $v) {
            DB::table('vendor_notifications')->insert([
                'vendor_user_id' => $v,
                'type' => 'NEW_REVIEW',
                'title' => 'New review received',
                'body' => 'A new review was submitted for your branch',
                'data' => null,
                'is_read' => false,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // admin notifications
        foreach (array_slice($admins,0,5) as $a) {
            DB::table('admin_notifications')->insert([
                'admin_id' => $a,
                'type' => 'SYSTEM_ALERT',
                'title' => 'System alert',
                'body' => 'Background job completed',
                'data' => null,
                'is_read' => false,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
