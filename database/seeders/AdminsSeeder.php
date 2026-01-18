<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AdminsSeeder extends Seeder
{
    public function run()
    {
        $existing = DB::table('admins')->where('email', 'admin@example.com')->first();
        if ($existing) {
            DB::table('admins')->where('id', $existing->id)->update([
                'name' => 'Super Admin',
                'phone' => null,
                'password_hash' => bcrypt('password'),
                'role' => 'SUPER_ADMIN',
                'updated_at' => now(),
            ]);
        } else {
            DB::table('admins')->insert([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'phone' => null,
                'password_hash' => bcrypt('password'),
                'role' => 'SUPER_ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
