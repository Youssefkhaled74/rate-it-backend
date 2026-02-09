<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserNotificationSender
{
    public function sendToUsers(array $userIds, array $payload, ?int $adminId = null, string $type = 'direct'): int
    {
        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        $userIds = array_filter($userIds, fn ($id) => $id > 0);
        if (count($userIds) === 0) return 0;

        // Resolve title/body
        if (! empty($payload['template_id'])) {
            $tpl = DB::table('notification_templates')->where('id', $payload['template_id'])->first();
            $title_en = $tpl->title_en ?? $tpl->title_tpl ?? null;
            $title_ar = $tpl->title_ar ?? null;
            $body_en = $tpl->body_en ?? $tpl->body_tpl ?? null;
            $body_ar = $tpl->body_ar ?? null;
            $tplId = $tpl->id ?? null;
        } else {
            $title_en = $payload['title_en'] ?? null;
            $title_ar = $payload['title_ar'] ?? null;
            $body_en = $payload['body_en'] ?? null;
            $body_ar = $payload['body_ar'] ?? null;
            $tplId = null;
        }

        $lang = $payload['lang'] ?? 'en';
        if ($lang === 'auto') $lang = 'en';

        $title = $lang === 'ar' ? ($title_ar ?? $title_en) : ($title_en ?? $title_ar);
        $body = $lang === 'ar' ? ($body_ar ?? $body_en) : ($body_en ?? $body_ar);

        $now = Carbon::now();
        $chunks = array_chunk($userIds, 500);
        $count = 0;

        foreach ($chunks as $chunk) {
            $rows = [];
            foreach ($chunk as $uid) {
                $rows[] = [
                    'user_id' => $uid,
                    'type' => $type,
                    'title' => $title,
                    'body' => $body,
                    'data' => json_encode(array_merge($payload['data'] ?? [], [
                        'source' => 'admin_bulk',
                        'template_id' => $tplId,
                        'admin_id' => $adminId,
                    ])),
                    'is_read' => false,
                    'sent_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('user_notifications')->insert($rows);
            $count += count($rows);
        }

        return $count;
    }
}
