<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\Branch;
use App\Models\BranchQrSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QrScanService
{
    public function scan($user, string $qrCodeValue, int $ttlMinutes = null)
    {
        $branch = Branch::where('qr_code_value', $qrCodeValue)->first();
        if (! $branch) {
            return null;
        }

        $ttl = $ttlMinutes ?? (int) config('reviews.qr_ttl_minutes', 10);
        $now = Carbon::now();
        // Invalidate previous non-consumed sessions for this user+branch
        BranchQrSession::where('user_id', $user->id)
            ->where('branch_id', $branch->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => Carbon::now()]);

        $session = BranchQrSession::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'qr_code_value' => $qrCodeValue,
            'session_token' => Str::random(64),
            'scanned_at' => $now,
            'expires_at' => $now->copy()->addMinutes($ttl),
        ]);

        // Debug log (temporary)
        Log::debug('qr.scan', ['token' => $session->session_token, 'expires_at' => $session->expires_at?->toDateTimeString(), 'now' => Carbon::now()->toDateTimeString(), 'user_id' => $user->id, 'branch_id' => $branch->id]);

        return compact('session','branch');
    }
}
