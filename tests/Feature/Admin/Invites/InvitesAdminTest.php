<?php

namespace Tests\Feature\Admin\Invites;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Invite;

class InvitesAdminTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test invites
        Invite::factory(5)->create();
    }

    /**
     * Test list invites
     */
    public function test_list_invites()
    {
        $response = $this->getAsAdmin('/api/v1/admin/invites');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'email',
                    'status',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * Test list invites with pagination
     */
    public function test_list_invites_with_pagination()
    {
        $response = $this->getAsAdmin('/api/v1/admin/invites?page=1&limit=10');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'page',
                'limit',
                'total',
            ],
        ]);
    }

    /**
     * Test filter invites by status
     */
    public function test_filter_invites_by_status()
    {
        // Create invites with different statuses
        Invite::factory()->create(['status' => 'pending']);
        Invite::factory()->create(['status' => 'accepted']);

        $response = $this->getAsAdmin('/api/v1/admin/invites?status=pending');

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $invite) {
            $this->assertEquals('pending', $invite['status']);
        }
    }

    /**
     * Test show invite details
     */
    public function test_show_invite()
    {
        $invite = Invite::first();

        $response = $this->getAsAdmin("/api/v1/admin/invites/{$invite->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'status',
                'created_at',
            ],
        ]);
    }

    /**
     * Test list invites without authentication fails
     */
    public function test_list_invites_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/invites');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show non-existent invite returns 404
     */
    public function test_show_non_existent_invite_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/invites/99999');

        $this->assertNotFoundJson($response);
    }
}
