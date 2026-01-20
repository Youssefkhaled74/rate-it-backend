<?php

namespace Tests\Feature\Admin\CatalogIntegrity;

use Tests\Feature\Admin\Support\AdminTestCase;

class CatalogIntegrityTest extends AdminTestCase
{
    /**
     * Test validate place-brand relationships (Task 02.1)
     */
    public function test_validate_place_brand_relationships()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog-integrity/validate-place-brand');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'issues' => [
                    '*' => [
                        'place_id',
                        'place_name',
                        'issue',
                    ],
                ],
                'total_issues',
            ],
        ]);
    }

    /**
     * Test validate place category/subcategory relationships (Task 02.2)
     */
    public function test_validate_place_category_relationships()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog-integrity/validate-place-category');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'issues' => [
                    '*' => [
                        'place_id',
                        'place_name',
                        'issue',
                    ],
                ],
                'total_issues',
            ],
        ]);
    }

    /**
     * Test validate branch QR codes (Task 02 - QR constraints)
     */
    public function test_validate_branch_qr_codes()
    {
        $response = $this->getAsAdmin('/api/v1/admin/catalog-integrity/validate-branch-qr');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'issues' => [
                    '*' => [
                        'branch_id',
                        'branch_name',
                        'issue',
                    ],
                ],
                'total_issues',
            ],
        ]);
    }

    /**
     * Test fix place-brand relationships helper
     */
    public function test_fix_place_brand_relationships()
    {
        $response = $this->postAsAdmin('/api/v1/admin/catalog-integrity/fix-place-brand', []);

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'fixed_count',
                'remaining_issues',
            ],
        ]);
    }

    /**
     * Test catalog integrity without authentication fails
     */
    public function test_catalog_integrity_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/catalog-integrity/validate-place-brand');

        $this->assertUnauthorizedJson($response);
    }
}
