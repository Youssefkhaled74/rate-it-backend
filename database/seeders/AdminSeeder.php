<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->insert([
            'id' => (string) Str::uuid(),
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
