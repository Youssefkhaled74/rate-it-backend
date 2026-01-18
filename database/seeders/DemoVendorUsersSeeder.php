<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\VendorUser;
use Illuminate\Database\Seeder;

class DemoVendorUsersSeeder extends Seeder
{
    public function run()
    {
        $brands = Brand::all();

        foreach ($brands as $brand) {
            // brand admin
            VendorUser::factory()->create(['brand_id' => $brand->id, 'role' => 'VENDOR_ADMIN']);
            // staff per branch
            $branches = Branch::whereIn('place_id', function($q) use ($brand) {
                $q->select('id')->from('places')->where('brand_id', $brand->id);
            })->get();

            foreach ($branches as $branch) {
                VendorUser::factory()->count(2)->create(['brand_id' => $brand->id, 'branch_id' => $branch->id, 'role' => 'BRANCH_STAFF']);
            }
        }
    }
}
