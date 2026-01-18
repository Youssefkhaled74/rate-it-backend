<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.users', 200);
        User::factory()->count($count)->create();
    }
}
