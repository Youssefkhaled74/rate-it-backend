<?php

namespace Tests\Feature\Admin\Notifications;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationsAdminTest extends AdminTestCase
{
    /**
     * Test list notification templates
     */
    public function test_list_notification_templates()
    {
        $response = $this->getAsAdmin('/api/v1/admin/notifications/templates');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'subject',
                    'body',
                ],
            ],
        ]);
    }

    /**
     * Test show notification template
     */
    public function test_show_notification_template()
    {
        $template = DB::table('notification_templates')->first();

        if ($template) {
            $response = $this->getAsAdmin("/api/v1/admin/notifications/templates/{$template->id}");

            $this->assertSuccessJson($response);
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'subject',
                    'body',
                ],
            ]);
        }
    }

    /**
     * Test create notification template
     */
    public function test_create_notification_template()
    {
        $response = $this->postAsAdmin('/api/v1/admin/notifications/templates', [
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'body' => 'Test notification body',
        ]);

        // May return 201 or 200
        $this->assertTrue($response->status() === 201 || $response->status() === 200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'subject',
                'body',
            ],
        ]);
    }

    /**
     * Test broadcast notification to all users
     */
    public function test_broadcast_notification()
    {
        // Create test users
        User::factory(3)->create();

        $response = $this->postAsAdmin('/api/v1/admin/notifications/broadcast', [
            'title' => 'Important Announcement',
            'body' => 'This is a broadcast message',
            'type' => 'general',
        ]);

        $this->assertSuccessJson($response);

        // Verify notifications were created
        $notificationCount = DB::table('user_notifications')
            ->whereDate('created_at', today())
            ->count();

        $this->assertGreaterThanOrEqual(3, $notificationCount);
    }

    /**
     * Test send notification to specific user
     */
    public function test_send_notification_to_user()
    {
        $user = User::factory()->create();

        $response = $this->postAsAdmin('/api/v1/admin/notifications/send-to-user', [
            'user_id' => $user->id,
            'title' => 'Personal Message',
            'body' => 'This is for you',
            'type' => 'personal',
        ]);

        $this->assertSuccessJson($response);

        // Verify notification was created for user
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test send notification fails without user_id
     */
    public function test_send_notification_fails_without_user()
    {
        $response = $this->postAsAdmin('/api/v1/admin/notifications/send-to-user', [
            'title' => 'Message',
            'body' => 'No user',
            'type' => 'personal',
        ]);

        $this->assertValidationErrorJson($response);
    }

    /**
     * Test list notifications without authentication fails
     */
    public function test_list_notifications_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/notifications/templates');

        $this->assertUnauthorizedJson($response);
    }
}
