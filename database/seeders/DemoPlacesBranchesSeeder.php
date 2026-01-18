<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Place;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPlacesBranchesSeeder extends Seeder
{
    public function run()
    {
        $placesCount = config('seeding.demo.places', 30);
        $brands = Brand::all();

        // create mix of branded and standalone places
        for ($i = 0; $i < $placesCount; $i++) {
            $brand = $brands->random(1)->first();
            $isBranded = rand(0,1) === 1 && $brands->count() > 0;
            $place = Place::factory()->create([
                'brand_id' => $isBranded ? $brand->id : null,
            ]);

            $branchesToCreate = rand(config('seeding.demo.branches_min',1), config('seeding.demo.branches_max',4));
            for ($b = 0; $b < $branchesToCreate; $b++) {
                Branch::factory()->create(['place_id' => $place->id]);
            }
        }
    }
}
