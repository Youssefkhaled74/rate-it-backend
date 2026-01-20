<?php

namespace Tests\Feature\Admin\Support;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;
    use InteractsWithAdminApi;

    protected string $adminToken = '';
    protected ?Admin $admin = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed test environment with admin and catalog data
        $this->seed([
            \Database\Seeders\RolesAndPermissionsSeeder::class,
            \Database\Seeders\AdminTestSeeder::class,
        ]);

        // Login as super admin for authenticated tests
        $this->loginAsAdmin('admin@test.local', 'password');
    }

    /**
     * Login as admin and store the token for subsequent requests.
     */
    protected function loginAsAdmin(string $email, string $password): void
    {
        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->adminToken = $response->json('data.token');
        $this->admin = Admin::where('email', $email)->first();
    }

    /**
     * Get authorization headers for authenticated requests.
     */
    protected function adminHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->adminToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get authorization headers with custom token.
     */
    protected function adminHeadersWithToken(string $token): array
    {
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Assert successful API response.
     */
    protected function assertSuccessJson($response, string $message = null)
    {
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta',
            ])
            ->assertJson(['success' => true]);

        if ($message) {
            $response->assertJsonFragment(['message' => $message]);
        }
    }

    /**
     * Assert created (201) API response.
     */
    protected function assertCreatedJson($response)
    {
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta',
            ])
            ->assertJson(['success' => true]);
    }

    /**
     * Assert error API response.
     */
    protected function assertErrorJson($response, int $status = 400, string $message = null)
    {
        $response->assertStatus($status)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta',
            ])
            ->assertJson(['success' => false]);

        if ($message) {
            $response->assertJsonFragment(['message' => $message]);
        }
    }

    /**
     * Assert unauthorized (401) response.
     */
    protected function assertUnauthorizedJson($response)
    {
        $this->assertErrorJson($response, 401);
    }

    /**
     * Assert forbidden (403) response.
     */
    protected function assertForbiddenJson($response)
    {
        $this->assertErrorJson($response, 403);
    }

    /**
     * Assert validation error (422) response.
     */
    protected function assertValidationErrorJson($response)
    {
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['errors'],
                'meta',
            ])
            ->assertJson(['success' => false]);
    }

    /**
     * Assert not found (404) response.
     */
    protected function assertNotFoundJson($response)
    {
        $this->assertErrorJson($response, 404);
    }
}
