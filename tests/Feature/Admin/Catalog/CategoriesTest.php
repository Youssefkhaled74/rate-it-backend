<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Category;

class CategoriesTest extends AdminTestCase
{
    /**
     * Test list categories
     */
    public function test_list_categories()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/categories');

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
     * Test list categories without auth fails
     */
    public function test_list_categories_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/catalog/categories');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show category
     */
    public function test_show_category()
    {
        $category = Category::where('name_en', 'Test Category')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/categories/{$category->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'is_active',
            ],
        ]);
        $response->assertJsonPath('data.name_en', 'Test Category');
    }

    /**
     * Test show non-existent category returns 404
     */
    public function test_show_non_existent_category_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/categories/99999');

        $this->assertNotFoundJson($response);
    }

    /**
     * Test create category with valid data
     */
    public function test_create_category_with_valid_data()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/categories', [
            'name_en' => 'New Category',
            'name_ar' => 'فئة جديدة',
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'is_active',
            ],
        ]);

        $this->assertDatabaseHas('categories', [
            'name_en' => 'New Category',
            'name_ar' => 'فئة جديدة',
        ]);
    }

    /**
     * Test create category fails without required fields
     */
    public function test_create_category_fails_without_name_en()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/categories', [
            'name_ar' => 'فئة بدون اسم إنجليزي',
            'is_active' => true,
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test update category
     */
    public function test_update_category()
    {
        $category = Category::where('name_en', 'Test Category')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/categories/{$category->id}", [
            'name_en' => 'Updated Category',
            'name_ar' => 'فئة محدثة',
            'is_active' => false,
        ]);

        $this->assertSuccessJson($response);
        $response->assertJsonPath('data.name_en', 'Updated Category');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name_en' => 'Updated Category',
            'is_active' => false,
        ]);
    }

    /**
     * Test delete category
     */
    public function test_delete_category()
    {
        $category = Category::create([
            'name_en' => 'Category to Delete',
            'name_ar' => 'فئة للحذف',
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/categories/{$category->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name_en' => 'Category to Delete',
        ]);
    }

    /**
     * Test filter categories by active status
     */
    public function test_filter_categories_by_active_status()
    {
        Category::create([
            'name_en' => 'Inactive Category',
            'name_ar' => 'فئة غير نشطة',
            'is_active' => false,
        ]);

        $response = $this->getAsAdmin('/api/v1/admin/catalog/categories?active=1');

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $category) {
            $this->assertTrue($category['is_active']);
        }
    }
}
