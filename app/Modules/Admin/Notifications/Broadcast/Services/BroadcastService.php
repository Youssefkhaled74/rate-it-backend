<?php

namespace App\Modules\Admin\Notifications\Broadcast\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BroadcastService
{
    public function broadcast(array $payload, $admin)
    {
        $audience = $payload['audience'] ?? ['type'=>'all'];
        $lang = $payload['lang'] ?? 'en';

        // Resolve title/body
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

        // Build user query
        $usersQ = DB::table('users')->select('users.id');
        if (isset($audience['type']) && $audience['type'] === 'segment') {
            $s = $audience['segment'] ?? [];
            if (! empty($s['created_from'])) $usersQ->whereDate('users.created_at', '>=', $s['created_from']);
            if (! empty($s['created_to'])) $usersQ->whereDate('users.created_at', '<=', $s['created_to']);

            if (isset($s['min_points']) || isset($s['max_points'])) {
                $usersQ->leftJoin('points_transactions as pt', 'pt.user_id', '=', 'users.id')
                    ->selectRaw('users.id, COALESCE(SUM(pt.points),0) as points')
                    ->groupBy('users.id');
                if (isset($s['min_points'])) $usersQ->havingRaw('points >= ?', [(int)$s['min_points']]);
                if (isset($s['max_points'])) $usersQ->havingRaw('points <= ?', [(int)$s['max_points']]);
            }
        }

        $ids = $usersQ->pluck('id')->toArray();
        $total = count($ids);
        $max = 50000; // safety cap
        if ($total > $max) {
            $ids = array_slice($ids, 0, $max);
            $total = count($ids);
        }

        $now = Carbon::now();
        $chunks = array_chunk($ids, 500);
        $queued = false;

        foreach ($chunks as $chunk) {
            $rows = [];
            foreach ($chunk as $uid) {
                $langUse = $lang === 'auto' ? request()->header('X-Lang','en') : $lang;
                $title = $langUse === 'ar' ? ($title_ar ?? $title_en) : ($title_en ?? $title_ar);
                $body = $langUse === 'ar' ? ($body_ar ?? $body_en) : ($body_en ?? $body_ar);
                $rows[] = [
                    'user_id' => $uid,
                    'type' => 'broadcast',
                    'title' => $title,
                    'body' => $body,
                    'data' => json_encode(array_merge($payload['data'] ?? [], ['source'=>'admin_broadcast','template_id'=>$tpl->id ?? null,'admin_id'=>$admin->id ?? null,'audience'=>$audience])),
                    'is_read' => false,
                    'sent_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('user_notifications')->insert($rows);
        }

        return ['recipients_count' => $total, 'queued' => $queued];
    }
}
