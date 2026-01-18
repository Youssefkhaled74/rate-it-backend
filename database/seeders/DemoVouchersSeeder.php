<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoVouchersSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.vouchers', 200);
        $users = DB::table('users')->pluck('id')->toArray();
        $brands = DB::table('brands')->pluck('id')->toArray();
        $branches = DB::table('branches')->pluck('id')->toArray();
        $vendorUsers = DB::table('vendor_users')->pluck('id')->toArray();

        for ($i = 0; $i < $count; $i++) {
            $user = $users[array_rand($users)];
            $brand = $brands[array_rand($brands)];
            $status = $this->randomStatus();

            $voucherData = [
                'user_id' => $user,
                'brand_id' => $brand,
                'code' => strtoupper(Str::random(10)) . $i,
                'points_used' => rand(50,500),
                'value_amount' => rand(5,200),
                'status' => $status,
                'issued_at' => now(),
                'expires_at' => now()->addDays(rand(1,30)),
                'used_at' => null,
                'used_branch_id' => null,
                'verified_by_vendor_user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($status === 'USED') {
                $voucherData['used_at'] = now();
                $voucherData['used_branch_id'] = $branches[array_rand($branches)];
                $voucherData['verified_by_vendor_user_id'] = $vendorUsers[array_rand($vendorUsers)];
            }

            $voucherId = DB::table('vouchers')->insertGetId($voucherData);

            if ($status === 'USED') {
                // create activity log for voucher redemption
                DB::table('activity_logs')->insert([
                    'actor_type' => 'VENDOR_USER',
                    'actor_user_id' => null,
                    'actor_admin_id' => null,
                    'actor_vendor_user_id' => $voucherData['verified_by_vendor_user_id'],
                    'action' => 'REDEEM_VOUCHER',
                    'entity_type' => 'VOUCHERS',
                    'entity_id' => $voucherId,
                    'ip_address' => null,
                    'user_agent' => null,
                    'meta' => json_encode(['branch_id' => $voucherData['used_branch_id']]),
                    'created_at' => now(),
                ]);
            }
        }
    }

    protected function randomStatus()
    {
        $r = rand(1,100);
        if ($r <= 60) return 'VALID';
        if ($r <= 85) return 'USED';
        return 'EXPIRED';
    }
}
