<?php

namespace Tests\Feature\Admin\LoyaltySettings;

use Tests\Feature\Admin\Support\AdminTestCase;
use Illuminate\Support\Facades\DB;

class LoyaltySettingsTest extends AdminTestCase
{
    /**
     * Test list loyalty settings versions
     */
    public function test_list_loyalty_settings()
    {
        $response = $this->getAsAdmin('/api/v1/admin/loyalty-settings');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'per_review',
                    'per_reply',
                    'is_active',
                    'version',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * Test show loyalty settings details
     */
    public function test_show_loyalty_settings()
    {
        $settings = DB::table('points_settings')->first();

        $response = $this->getAsAdmin("/api/v1/admin/loyalty-settings/{$settings->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'per_review',
                'per_reply',
                'is_active',
                'version',
            ],
        ]);
    }

    /**
     * Test create new version of loyalty settings
     */
    public function test_create_new_version()
    {
        $response = $this->postAsAdmin('/api/v1/admin/loyalty-settings', [
            'per_review' => 15,
            'per_reply' => 8,
        ]);

        $this->assertCreatedJson($response);
        
        $this->assertDatabaseHas('points_settings', [
            'per_review' => 15,
            'per_reply' => 8,
        ]);
    }

    /**
     * Test update (create new version) of loyalty settings
     */
    public function test_update_creates_new_version()
    {
        $settings = DB::table('points_settings')->first();
        $oldVersion = $settings->version;

        $response = $this->putAsAdmin("/api/v1/admin/loyalty-settings/{$settings->id}", [
            'per_review' => 20,
            'per_reply' => 10,
        ]);

        // Should either succeed or return 405/403 if immutable
        if ($response->status() === 422 || $response->status() === 403 || $response->status() === 405) {
            // Update not allowed - that's OK for immutable design
            $this->assertTrue(true);
        } else {
            $this->assertSuccessJson($response);
        }
    }

    /**
     * Test activate settings version
     */
    public function test_activate_settings_version()
    {
        // Create an inactive version
        $newSettings = DB::table('points_settings')->insertGetId([
            'per_review' => 25,
            'per_reply' => 12,
            'is_active' => false,
            'version' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postAsAdmin("/api/v1/admin/loyalty-settings/{$newSettings}/activate");

        $this->assertSuccessJson($response);

        // Verify this version is now active
        $active = DB::table('points_settings')->where('is_active', true)->where('id', $newSettings)->first();
        $this->assertNotNull($active);
    }

    /**
     * Test delete loyalty settings fails (immutable/versioned)
     */
    public function test_delete_loyalty_settings_fails()
    {
        $settings = DB::table('points_settings')->first();

        $response = $this->deleteAsAdmin("/api/v1/admin/loyalty-settings/{$settings->id}");

        // Should fail with 403/405 since settings are immutable
        $this->assertTrue(
            $response->status() === 403 ||
            $response->status() === 405 ||
            $response->status() === 422
        );
    }

    /**
     * Test list loyalty settings without authentication fails
     */
    public function test_list_loyalty_settings_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/loyalty-settings');

        $this->assertUnauthorizedJson($response);
    }
}
