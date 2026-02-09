<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendUserNotificationsJob;
use App\Services\Admin\UserNotificationSender;
use Illuminate\Http\Request;

class NotificationsPageController extends Controller
{
    public function create()
    {
        return view('admin.notifications.send');
    }

    public function store(Request $request, UserNotificationSender $sender)
    {
        $data = $request->validate([
            'audience' => ['required', 'in:single,multiple'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'user_ids' => ['nullable', 'string'],
            'lang' => ['required', 'in:en,ar,auto'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'title_ar' => ['nullable', 'string', 'max:200'],
            'body_en' => ['nullable', 'string', 'max:2000'],
            'body_ar' => ['nullable', 'string', 'max:2000'],
            'use_queue' => ['nullable', 'boolean'],
        ]);

        $userIds = [];
        if ($data['audience'] === 'single') {
            if (empty($data['user_id'])) {
                return back()->withErrors(['user_id' => 'User ID is required.'])->withInput();
            }
            $userIds = [(int) $data['user_id']];
        } else {
            $raw = (string) ($data['user_ids'] ?? '');
            preg_match_all('/\\d+/', $raw, $m);
            $userIds = array_map('intval', $m[0] ?? []);
            $userIds = array_values(array_unique(array_filter($userIds)));
            if (count($userIds) === 0) {
                return back()->withErrors(['user_ids' => 'Please provide at least one user ID.'])->withInput();
            }
        }

        if (empty($data['title_en']) && empty($data['title_ar'])) {
            return back()->withErrors(['title_en' => 'Title is required in EN or AR.'])->withInput();
        }
        if (empty($data['body_en']) && empty($data['body_ar'])) {
            return back()->withErrors(['body_en' => 'Body is required in EN or AR.'])->withInput();
        }

        $payload = [
            'lang' => $data['lang'],
            'title_en' => $data['title_en'] ?? null,
            'title_ar' => $data['title_ar'] ?? null,
            'body_en' => $data['body_en'] ?? null,
            'body_ar' => $data['body_ar'] ?? null,
        ];

        $adminId = auth()->guard('admin_web')->id();
        $useQueue = (bool) ($data['use_queue'] ?? false);
        $threshold = 500;

        if ($useQueue || count($userIds) > $threshold) {
            SendUserNotificationsJob::dispatch($userIds, $payload, $adminId);
            return back()->with('success', 'Notification queued for ' . count($userIds) . ' users.');
        }

        $sent = $sender->sendToUsers($userIds, $payload, $adminId, 'direct');
        return back()->with('success', 'Notification sent to ' . $sent . ' users.');
    }
}
