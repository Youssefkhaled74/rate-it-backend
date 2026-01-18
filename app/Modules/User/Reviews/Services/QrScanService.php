<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\Branch;
use App\Models\BranchQrSession;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QrScanService
{
    public function scan($user, string $qrCodeValue, int $ttlMinutes = 10)
    {
        $branch = Branch::where('qr_code_value', $qrCodeValue)->first();
        if (! $branch) {
            return null;
        }

        $now = Carbon::now();
        $session = BranchQrSession::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'qr_code_value' => $qrCodeValue,
            'session_token' => Str::random(48),
            'scanned_at' => $now,
            'expires_at' => $now->copy()->addMinutes($ttlMinutes),
        ]);

        return compact('session','branch');
    }
}
