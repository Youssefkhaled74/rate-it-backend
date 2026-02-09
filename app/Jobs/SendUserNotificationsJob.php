<?php

namespace App\Jobs;

use App\Services\Admin\UserNotificationSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $userIds;
    public array $payload;
    public ?int $adminId;

    public function __construct(array $userIds, array $payload, ?int $adminId = null)
    {
        $this->userIds = $userIds;
        $this->payload = $payload;
        $this->adminId = $adminId;
    }

    public function handle(UserNotificationSender $sender): void
    {
        $sender->sendToUsers($this->userIds, $this->payload, $this->adminId, 'bulk');
    }
}
