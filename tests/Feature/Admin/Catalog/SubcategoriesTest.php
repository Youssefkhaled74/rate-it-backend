<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Subcategory;
use App\Models\Category;

class SubcategoriesTest extends AdminTestCase
{
    /**
     * Test list subcategories
     */
    public function test_list_subcategories()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/subcategories');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'category_id',
                    'is_active',
                ],
            ],
        ]);
    }

    /**
     * Test show subcategory
     */
    public function test_show_subcategory()
    {
        $subcategory = Subcategory::where('name_en', 'Test Subcategory')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/subcategories/{$subcategory->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'category_id',
                'is_active',
            ],
        ]);
    }

    /**
     * Test create subcategory
     */
    public function test_create_subcategory()
    {
        $category = Category::where('name_en', 'Test Category')->first();

        $response = $this->postAsAdmin('/api/v1/admin/catalog/subcategories', [
            'name_en' => 'New Subcategory',
            'name_ar' => 'فئة فرعية جديدة',
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('subcategories', [
            'name_en' => 'New Subcategory',
            'category_id' => $category->id,
        ]);
    }

    /**
     * Test create subcategory fails without category_id
     */
    public function test_create_subcategory_fails_without_category_id()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/subcategories', [
            'name_en' => 'Invalid Subcategory',
            'name_ar' => 'فئة فرعية غير صالحة',
            'is_active' => true,
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test update subcategory
     */
    public function test_update_subcategory()
    {
        $subcategory = Subcategory::where('name_en', 'Test Subcategory')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/subcategories/{$subcategory->id}", [
            'name_en' => 'Updated Subcategory',
            'name_ar' => 'فئة فرعية محدثة',
            'is_active' => false,
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'name_en' => 'Updated Subcategory',
            'is_active' => false,
        ]);
    }

    /**
     * Test delete subcategory
     */
    public function test_delete_subcategory()
    {
        $category = Category::where('name_en', 'Test Category')->first();
        $subcategory = Subcategory::create([
            'name_en' => 'Subcategory to Delete',
            'name_ar' => 'فئة فرعية للحذف',
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/subcategories/{$subcategory->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('subcategories', [
            'id' => $subcategory->id,
        ]);
    }
}
