<?php

namespace Modules\Admin\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\app\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default super admin from env variables
        Admin::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@rateit.com')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'password123')),
                'phone' => env('ADMIN_PHONE', null),
                'is_super' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create additional sample admins (optional)
        Admin::factory()->count(3)->create();
    }
}
