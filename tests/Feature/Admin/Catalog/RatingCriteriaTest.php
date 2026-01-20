<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;

class RatingCriteriaTest extends AdminTestCase
{
    /**
     * Test list rating criteria
     */
    public function test_list_rating_criteria()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/rating-criteria');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'type',
                    'is_active',
                ],
            ],
        ]);
    }

    /**
     * Test show rating criteria
     */
    public function test_show_rating_criteria()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'type',
                'is_active',
            ],
        ]);
    }

    /**
     * Test create rating criteria with RATING type
     */
    public function test_create_criteria_with_rating_type()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/rating-criteria', [
            'name_en' => 'Quality Rating',
            'name_ar' => 'تقييم الجودة',
            'type' => 'RATING',
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('rating_criteria', [
            'name_en' => 'Quality Rating',
            'type' => 'RATING',
        ]);
    }

    /**
     * Test create rating criteria with MULTIPLE_CHOICE type
     */
    public function test_create_criteria_with_multiple_choice_type()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/rating-criteria', [
            'name_en' => 'Service Options',
            'name_ar' => 'خيارات الخدمة',
            'type' => 'MULTIPLE_CHOICE',
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('rating_criteria', [
            'name_en' => 'Service Options',
            'type' => 'MULTIPLE_CHOICE',
        ]);
    }

    /**
     * Test create criteria fails with invalid type
     */
    public function test_create_criteria_fails_with_invalid_type()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog/rating-criteria', [
            'name_en' => 'Invalid Criteria',
            'name_ar' => 'معايير غير صالحة',
            'type' => 'INVALID_TYPE',
            'is_active' => true,
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test update rating criteria
     */
    public function test_update_rating_criteria()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}", [
            'name_en' => 'Updated Criteria',
            'name_ar' => 'معايير محدثة',
            'is_active' => false,
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('rating_criteria', [
            'id' => $criteria->id,
            'name_en' => 'Updated Criteria',
        ]);
    }

    /**
     * Test delete rating criteria
     */
    public function test_delete_rating_criteria()
    {
        $criteria = RatingCriteria::create([
            'name_en' => 'Criteria to Delete',
            'name_ar' => 'معايير للحذف',
            'type' => 'RATING',
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('rating_criteria', [
            'id' => $criteria->id,
        ]);
    }

    /**
     * Test filter criteria by type
     */
    public function test_filter_criteria_by_type()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/rating-criteria?type=RATING');

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $item) {
            $this->assertEquals('RATING', $item['type']);
        }
    }
}

class RatingCriteriaChoicesTest extends AdminTestCase
{
    /**
     * Test list rating criteria choices
     */
    public function test_list_rating_criteria_choices()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}/choices");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'value',
                ],
            ],
        ]);
    }

    /**
     * Test create choice for criteria
     */
    public function test_create_choice_for_criteria()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();

        $response = $this->postAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}/choices", [
            'name_en' => 'New Choice',
            'name_ar' => 'خيار جديد',
            'value' => 5,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('rating_criteria_choices', [
            'criteria_id' => $criteria->id,
            'name_en' => 'New Choice',
            'value' => 5,
        ]);
    }

    /**
     * Test choice value must be integer
     */
    public function test_choice_value_must_be_integer()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();

        $response = $this->postAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}/choices", [
            'name_en' => 'Bad Value',
            'name_ar' => 'قيمة سيئة',
            'value' => 'not_integer',
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test choice value must be unique per criteria
     */
    public function test_choice_value_must_be_unique_per_criteria()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();
        $existingChoice = RatingCriteriaChoice::where('criteria_id', $criteria->id)->first();

        // Try to create another choice with same value
        $response = $this->postAsAdmin("/api/v1/admin/catalog/rating-criteria/{$criteria->id}/choices", [
            'name_en' => 'Duplicate Value',
            'name_ar' => 'قيمة مكررة',
            'value' => $existingChoice->value,
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test update choice
     */
    public function test_update_choice()
    {
        $choice = RatingCriteriaChoice::first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/rating-criteria/choices/{$choice->id}", [
            'name_en' => 'Updated Choice',
            'name_ar' => 'خيار محدث',
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('rating_criteria_choices', [
            'id' => $choice->id,
            'name_en' => 'Updated Choice',
        ]);
    }

    /**
     * Test delete choice
     */
    public function test_delete_choice()
    {
        $criteria = RatingCriteria::where('name_en', 'Test Criteria')->first();
        $choice = RatingCriteriaChoice::create([
            'criteria_id' => $criteria->id,
            'name_en' => 'Choice to Delete',
            'name_ar' => 'خيار للحذف',
            'value' => 99,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/rating-criteria/choices/{$choice->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('rating_criteria_choices', [
            'id' => $choice->id,
        ]);
    }
}
