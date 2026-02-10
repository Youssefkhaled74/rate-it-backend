<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoQrSessionsSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.qr_sessions', 300);
        $users = DB::table('users')->pluck('id')->toArray();
        $branches = DB::table('branches')->pluck('id')->toArray();

        if (empty($users) || empty($branches)) {
            return;
        }

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $userId = $users[array_rand($users)];
            $branchId = $branches[array_rand($branches)];
            $scannedAt = now()->subDays(rand(0, 60))->subMinutes(rand(0, 1440));
            $expiresAt = (clone $scannedAt)->addMinutes(30);

            $rows[] = [
                'user_id' => $userId,
                'branch_id' => $branchId,
                'qr_code_value' => Str::uuid()->toString(),
                'session_token' => Str::uuid()->toString(),
                'scanned_at' => $scannedAt,
                'expires_at' => $expiresAt,
                'consumed_at' => rand(0, 1) ? $scannedAt->addMinutes(rand(1, 10)) : null,
                'created_at' => $scannedAt,
                'updated_at' => $scannedAt,
            ];
        }

        DB::table('branch_qr_sessions')->insert($rows);
    }
}
