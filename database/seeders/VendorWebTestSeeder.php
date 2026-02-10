<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Place;
use App\Models\Branch;
use App\Models\VendorUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorWebTestSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::first() ?? Brand::factory()->create(['name' => 'Demo Brand']);
        $place = Place::first() ?? Place::factory()->create(['brand_id' => $brand->id, 'name' => 'Demo Place']);
        $branch = Branch::first() ?? Branch::factory()->create(['place_id' => $place->id, 'name' => 'Demo Branch']);

        $adminPhone = '0500000001';
        $staffPhone = '0500000002';
        $password = 'secret';

        VendorUser::updateOrCreate(
            ['phone' => $adminPhone],
            [
                'brand_id' => $brand->id,
                'branch_id' => null,
                'name' => 'Vendor Admin',
                'email' => 'vendor.admin@example.com',
                'password_hash' => Hash::make($password),
                'role' => 'VENDOR_ADMIN',
                'is_active' => true,
            ]
        );

        VendorUser::updateOrCreate(
            ['phone' => $staffPhone],
            [
                'brand_id' => $brand->id,
                'branch_id' => $branch->id,
                'name' => 'Branch Staff',
                'email' => 'branch.staff@example.com',
                'password_hash' => Hash::make($password),
                'role' => 'BRANCH_STAFF',
                'is_active' => true,
            ]
        );
    }
}
