<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSubscriptionsSeeder extends Seeder
{
    public function run()
    {
        $users = User::inRandomOrder()->limit(100)->get();
        foreach ($users as $u) {
            Subscription::factory()->create(['user_id' => $u->id]);
        }
    }
}
