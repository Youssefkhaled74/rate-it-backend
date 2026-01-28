<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AdminsSeeder extends Seeder
{
    public function run()
    {
        // Ensure the default super admin exists
        $this->upsertAdmin([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'phone' => null,
            'password_hash' => bcrypt('password'),
            'role' => 'SUPER_ADMIN',
        ]);

        // Ensure the requested test admin exists (youssef@rateit.com)
        $this->upsertAdmin([
            'name' => 'Youssef Test',
            'email' => 'youssef@rateit.com',
            'phone' => null,
            'password_hash' => bcrypt('12345678#Rr'),
            'role' => 'SUPER_ADMIN',
            'is_active' => 1,
        ]);
    }

    private function upsertAdmin(array $data)
    {
        $existing = DB::table('admins')->where('email', $data['email'])->first();
        $now = now();

        if ($existing) {
            $update = array_merge($data, ['updated_at' => $now]);
            DB::table('admins')->where('id', $existing->id)->update($update);
        } else {
            $insert = array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
            DB::table('admins')->insert($insert);
        }
    }
}
