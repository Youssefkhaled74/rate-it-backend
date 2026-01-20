<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Brand;

class BrandsTest extends AdminTestCase
{
    /**
     * Test list brands
     */
    public function test_list_brands()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/brands');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'is_active',
                ],
            ],
        ]);
    }

    /**
     * Test show brand
     */
    public function test_show_brand()
    {
        $brand = Brand::where('name_en', 'Test Brand')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/brands/{$brand->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'is_active',
            ],
        ]);
    }

    /**
     * Test create brand
     */
    public function test_create_brand()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/brands', [
            'name_en' => 'New Brand',
            'name_ar' => 'علامة تجارية جديدة',
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('brands', [
            'name_en' => 'New Brand',
        ]);
    }

    /**
     * Test update brand
     */
    public function test_update_brand()
    {
        $brand = Brand::where('name_en', 'Test Brand')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/brands/{$brand->id}", [
            'name_en' => 'Updated Brand',
            'name_ar' => 'علامة تجارية محدثة',
            'is_active' => false,
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name_en' => 'Updated Brand',
        ]);
    }

    /**
     * Test delete brand
     */
    public function test_delete_brand()
    {
        $brand = Brand::create([
            'name_en' => 'Brand to Delete',
            'name_ar' => 'علامة تجارية للحذف',
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/brands/{$brand->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id,
        ]);
    }
}
