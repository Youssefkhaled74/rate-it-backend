<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'WELCOME','NEW_POINTS','LEVEL_ACHIEVED','POINTS_EXPIRY','VOUCHER_REDEEMED',
            'NEW_BRAND','SUBSCRIPTION_EXPIRY_7D','SUBSCRIPTION_EXPIRY_3D','SUBSCRIPTION_EXPIRY_1D','SUBSCRIPTION_RENEWED'
        ];

        foreach ($types as $type) {
            DB::table('notification_templates')->insert([
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
