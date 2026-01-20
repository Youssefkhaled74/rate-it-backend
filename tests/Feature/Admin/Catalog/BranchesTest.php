<?php

namespace Tests\Feature\Admin\Catalog;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Branch;
use App\Models\Place;
use Illuminate\Support\Str;

class BranchesTest extends AdminTestCase
{
    /**
     * Test list branches
     */
    public function test_list_branches()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog/branches');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'place_id',
                    'is_active',
                    'qr_code_value',
                ],
            ],
        ]);
    }

    /**
     * Test show branch
     */
    public function test_show_branch()
    {
        $branch = Branch::where('name_en', 'Test Branch')->first();

        $response = $this->getAsAdmin("/api/v1/admin/catalog/branches/{$branch->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'name_ar',
                'place_id',
                'is_active',
                'qr_code_value',
            ],
        ]);
    }

    /**
     * Test create branch
     */
    public function test_create_branch()
    {
        $place = Place::where('name_en', 'Test Place')->first();

        $response = $this->postAsAdmin('/api/v1/admin/catalog/branches', [
            'name_en' => 'New Branch',
            'name_ar' => 'فرع جديد',
            'place_id' => $place->id,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        $this->assertCreatedJson($response);

        $this->assertDatabaseHas('branches', [
            'name_en' => 'New Branch',
            'place_id' => $place->id,
        ]);
    }

    /**
     * Test update branch
     */
    public function test_update_branch()
    {
        $branch = Branch::where('name_en', 'Test Branch')->first();

        $response = $this->putAsAdmin("/api/v1/admin/catalog/branches/{$branch->id}", [
            'name_en' => 'Updated Branch',
            'name_ar' => 'فرع محدث',
            'is_active' => false,
        ]);

        $this->assertSuccessJson($response);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name_en' => 'Updated Branch',
        ]);
    }

    /**
     * Test delete branch
     */
    public function test_delete_branch()
    {
        $place = Place::where('name_en', 'Test Place')->first();
        $branch = Branch::create([
            'name_en' => 'Branch to Delete',
            'name_ar' => 'فرع للحذف',
            'place_id' => $place->id,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
            'qr_code_value' => 'DELETE_QR_' . Str::random(16),
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/catalog/branches/{$branch->id}");

        $this->assertSuccessJson($response);

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }

    /**
     * Test regenerate branch QR code
     */
    public function test_regenerate_branch_qr_code()
    {
        $branch = Branch::where('name_en', 'Test Branch')->first();
        $oldQr = $branch->qr_code_value;

        $response = $this->postAsAdmin("/api/v1/admin/catalog/branches/{$branch->id}/regenerate-qr");

        $this->assertSuccessJson($response);

        // Refresh and check QR was updated
        $branch->refresh();
        $this->assertNotEquals($oldQr, $branch->qr_code_value);
        $this->assertNotNull($branch->qr_generated_at);
    }

    /**
     * Test QR code must be unique (Task 02 - constraint)
     */
    public function test_qr_code_must_be_unique()
    {
        $place = Place::where('name_en', 'Test Place')->first();
        $existingBranch = Branch::where('name_en', 'Test Branch')->first();

        // Try to create branch with same QR code
        $response = $this->postAsAdmin('/api/v1/admin/catalog/branches', [
            'name_en' => 'Duplicate QR Branch',
            'name_ar' => 'فرع بـ QR مكرر',
            'place_id' => $place->id,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'qr_code_value' => $existingBranch->qr_code_value, // Same QR
            'is_active' => true,
        ]);

        $this->assertValidationErrorJson($response);
    }
}
