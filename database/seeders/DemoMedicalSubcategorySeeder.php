<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class DemoMedicalSubcategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create or update the parent category
        $category = Category::updateOrCreate(
            ['name_en' => 'Medical & Healthcare'],
            [
                'name_ar' => 'الرعاية الطبية والصحية',
                'is_active' => true,
            ]
        );

        // Create or update the Clinics subcategory under the category
        $subcategory = Subcategory::updateOrCreate(
            ['category_id' => $category->id, 'name_en' => 'Clinics'],
            [
                'name_ar' => 'عيادات',
                'is_active' => true,
            ]
        );

        $this->command->info("Demo category and subcategory ensured: category_id={$category->id}, subcategory_id={$subcategory->id}");
    }
}
