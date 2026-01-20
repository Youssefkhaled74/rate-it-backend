<?php

namespace App\Modules\Admin\Notifications\Send\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendService
{
    public function sendToUser(int $userId, array $payload, $admin)
    {
        $user = DB::table('users')->where('id',$userId)->first();
        if (! $user) return null;

        if (! empty($payload['template_id'])) {
            $tpl = DB::table('notification_templates')->where('id',$payload['template_id'])->first();
            $title_en = $tpl->title_en ?? $tpl->title_tpl;
            $title_ar = $tpl->title_ar ?? null;
            $body_en = $tpl->body_en ?? $tpl->body_tpl;
            $body_ar = $tpl->body_ar ?? null;
        } else {
            $title_en = $payload['title_en'] ?? null;
            $title_ar = $payload['title_ar'] ?? null;
            $body_en = $payload['body_en'] ?? null;
            $body_ar = $payload['body_ar'] ?? null;
            $tpl = null;
        }

        $langUse = ($payload['lang'] ?? 'en') === 'auto' ? request()->header('X-Lang','en') : ($payload['lang'] ?? 'en');
        $title = $langUse === 'ar' ? ($title_ar ?? $title_en) : ($title_en ?? $title_ar);
        $body = $langUse === 'ar' ? ($body_ar ?? $body_en) : ($body_en ?? $body_ar);

        $now = Carbon::now();
        $row = [
            'user_id' => $userId,
            'type' => 'direct',
            'title' => $title,
            'body' => $body,
            'data' => json_encode(array_merge($payload['data'] ?? [], ['source'=>'admin_direct','template_id'=>$tpl->id ?? null,'admin_id'=>$admin->id ?? null])),
            'is_read' => false,
            'sent_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $id = DB::table('user_notifications')->insertGetId($row);
        return DB::table('user_notifications')->where('id',$id)->first();
    }
}
