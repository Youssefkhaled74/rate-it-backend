<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoBrandsSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.brands', 10);
        Brand::factory()->count($count)->create();
    }
}
