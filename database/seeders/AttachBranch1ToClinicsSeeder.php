<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Place;
use App\Models\Subcategory;

class AttachBranch1ToClinicsSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::find(1);
        if (! $branch) {
            $this->command->error('Branch with id=1 not found. Create a branch with id=1 before running this seeder.');
            return;
        }

        // Ensure branch has a place
        $place = $branch->place;
        if (! $place) {
            $place = Place::create([
                'name_en' => 'Demo Clinic Place',
                'name_ar' => 'موقع تجريبي للعيادة',
            ]);
            $branch->place_id = $place->id;
            $branch->save();
            $this->command->info("Created demo place id={$place->id} and attached to branch id=1");
        }

        // Find Clinics subcategory
        $subcategory = Subcategory::where('name_en', 'Clinics')->first();
        if (! $subcategory) {
            $this->command->error('Clinics subcategory not found. Run DemoMedicalSubcategorySeeder first.');
            return;
        }

        // Attach subcategory to the place (idempotent)
        if ($place->subcategory_id !== $subcategory->id) {
            $place->subcategory_id = $subcategory->id;
            $place->save();
            $this->command->info("Associated place id={$place->id} with clinics subcategory_id={$subcategory->id}");
        } else {
            $this->command->info("Place id={$place->id} already associated with subcategory_id={$subcategory->id}");
        }
    }
}
