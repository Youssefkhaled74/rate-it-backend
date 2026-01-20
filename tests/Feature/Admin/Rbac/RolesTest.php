<?php

namespace Tests\Feature\Admin\Rbac;

use Tests\Feature\Admin\Support\AdminTestCase;
use Illuminate\Support\Facades\DB;

class RolesTest extends AdminTestCase
{
    /**
     * Test list roles
     */
    public function test_list_roles()
    {
        $response = $this->getAsAdmin('/api/v1/admin/rbac/roles');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
        ]);
        $response->assertJsonCount(6, 'data'); // SUPER_ADMIN, ADMIN, SUPPORT, FINANCE, VENDOR_ADMIN, BRANCH_STAFF
    }

    /**
     * Test list roles without authentication fails
     */
    public function test_list_roles_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/rbac/roles');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show role details
     */
    public function test_show_role_details()
    {
        $superAdminRole = DB::table('roles')->where('name', 'SUPER_ADMIN')->first();

        $response = $this->getAsAdmin("/api/v1/admin/rbac/roles/{$superAdminRole->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
        $response->assertJsonPath('data.name', 'SUPER_ADMIN');
    }

    /**
     * Test show non-existent role returns 404
     */
    public function test_show_non_existent_role_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/rbac/roles/99999');

        $this->assertNotFoundJson($response);
    }

    /**
     * Test create new role
     */
    public function test_create_new_role()
    {
        $response = $this->postAsAdmin('/api/v1/admin/rbac/roles', [
            'name' => 'CUSTOM_ROLE',
            'description' => 'Custom Test Role',
        ]);

        $this->assertCreatedJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
        $response->assertJsonPath('data.name', 'CUSTOM_ROLE');

        // Verify in database
        $this->assertDatabaseHas('roles', [
            'name' => 'CUSTOM_ROLE',
            'description' => 'Custom Test Role',
        ]);
    }

    /**
     * Test sync role permissions
     */
    public function test_sync_role_permissions()
    {
        $adminRole = DB::table('roles')->where('name', 'ADMIN')->first();
        $permissions = DB::table('permissions')->limit(3)->pluck('id')->toArray();

        $response = $this->postAsAdmin("/api/v1/admin/rbac/roles/{$adminRole->id}/sync-permissions", [
            'permission_ids' => $permissions,
        ]);

        $this->assertSuccessJson($response);

        // Verify permissions were synced
        $assignedPermissions = DB::table('role_has_permissions')
            ->where('role_id', $adminRole->id)
            ->pluck('permission_id')
            ->toArray();

        $this->assertEquals(count($permissions), count($assignedPermissions));
        foreach ($permissions as $permId) {
            $this->assertContains($permId, $assignedPermissions);
        }
    }
}

class PermissionsTest extends AdminTestCase
{
    /**
     * Test list permissions
     */
    public function test_list_permissions()
    {
        $response = $this->getAsAdmin('/api/v1/admin/rbac/permissions');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
        ]);
        // Should have multiple permissions
        $this->assertGreaterThan(5, count($response->json('data')));
    }

    /**
     * Test list permissions without authentication fails
     */
    public function test_list_permissions_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/rbac/permissions');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show permission details
     */
    public function test_show_permission_details()
    {
        $permission = DB::table('permissions')->where('name', 'dashboard.view')->first();

        $response = $this->getAsAdmin("/api/v1/admin/rbac/permissions/{$permission->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
        $response->assertJsonPath('data.name', 'dashboard.view');
    }

    /**
     * Test show non-existent permission returns 404
     */
    public function test_show_non_existent_permission_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/rbac/permissions/99999');

        $this->assertNotFoundJson($response);
    }

    /**
     * Test create new permission
     */
    public function test_create_new_permission()
    {
        $response = $this->postAsAdmin('/api/v1/admin/rbac/permissions', [
            'name' => 'custom.action',
            'description' => 'Custom Action Permission',
        ]);

        $this->assertCreatedJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
        $response->assertJsonPath('data.name', 'custom.action');

        // Verify in database
        $this->assertDatabaseHas('permissions', [
            'name' => 'custom.action',
            'description' => 'Custom Action Permission',
        ]);
    }
}
