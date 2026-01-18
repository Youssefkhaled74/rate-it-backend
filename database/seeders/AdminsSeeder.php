<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminsSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Super Admin',
                'phone' => null,
                'password_hash' => bcrypt('password'),
                'role' => 'SUPER_ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
