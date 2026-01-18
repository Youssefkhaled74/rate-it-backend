<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationTemplatesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'WELCOME','NEW_POINTS','LEVEL_ACHIEVED','POINTS_EXPIRY','VOUCHER_REDEEMED',
            'NEW_BRAND','SUBSCRIPTION_EXPIRY_7D','SUBSCRIPTION_EXPIRY_3D','SUBSCRIPTION_EXPIRY_1D','SUBSCRIPTION_RENEWED'
        ];

        foreach ($types as $type) {
            $existing = DB::table('notification_templates')->where('type', $type)->first();
            if ($existing) {
                DB::table('notification_templates')->where('id', $existing->id)->update([
                    'title_tpl' => $type . ' Title',
                    'body_tpl' => $type . ' body',
                    'channel' => 'PUSH',
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('notification_templates')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => $type,
                    'title_tpl' => $type . ' Title',
                    'body_tpl' => $type . ' body',
                    'channel' => 'PUSH',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
