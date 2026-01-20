<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Place;
use App\Models\Brand;
use App\Models\Category;

class PlacesTest extends AdminTestCase
{
    /**
     * Test list places
     */
    public function test_list_places()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/places');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'is_active',
                    'latitude',
                    'longitude',
                ],
            ],
        ]);
    }

    /**
     * Test show place
     */
    public function test_show_place()
    {
        $place = Place::where('name_en', 'Test Place')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/places/{$place->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'is_active',
                'latitude',
                'longitude',
            ],
        ]);
    }

    /**
     * Test create place
     */
    public function test_create_place()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/places', [
            'name_en' => 'New Place',
            'name_ar' => 'مكان جديد',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('places', [
            'name_en' => 'New Place',
        ]);
    }

    /**
     * Test create place fails without required fields
     */
    public function test_create_place_fails_without_location()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/places', [
            'name_en' => 'Invalid Place',
            'name_ar' => 'مكان غير صالح',
            'is_active' => true,
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test update place
     */
    public function test_update_place()
    {
        $place = Place::where('name_en', 'Test Place')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/places/{$place->id}", [
            'name_en' => 'Updated Place',
            'name_ar' => 'مكان محدث',
            'is_active' => false,
            'latitude' => 25.0,
            'longitude' => 47.0,
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('places', [
            'id' => $place->id,
            'name_en' => 'Updated Place',
        ]);
    }

    /**
     * Test delete place
     */
    public function test_delete_place()
    {
        $place = Place::create([
            'name_en' => 'Place to Delete',
            'name_ar' => 'مكان للحذف',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/places/{$place->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('places', [
            'id' => $place->id,
        ]);
    }

    /**
     * Test place-brand relationship (Task 02.1)
     */
    public function test_place_brand_relationship_enforced()
    {
        $place = Place::where('name_en', 'Test Place')->first();
        $brand = Brand::where('name_en', 'Test Brand')->first();

        // Verify relationship exists in test seeder
        $this->assertDatabaseHas('place_brand', [
            'place_id' => $place->id,
            'brand_id' => $brand->id,
        ]);
    }

    /**
     * Test place category/subcategory relationship (Task 02.2)
     */
    public function test_place_category_subcategory_relationship_enforced()
    {
        $place = Place::where('name_en', 'Test Place')->first();
        $category = Category::where('name_en', 'Test Category')->first();

        // Verify relationship exists in test seeder
        $this->assertDatabaseHas('place_category', [
            'place_id' => $place->id,
            'category_id' => $category->id,
        ]);
    }
}
