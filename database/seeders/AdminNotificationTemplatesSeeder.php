<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminNotificationTemplatesSeeder extends Seeder
{
    public function run()
    {
        $samples = [
            [
                'key' => 'promo_broadcast',
                'type' => 'PROMO_BROADCAST',
                'title_en' => 'Special Offer',
                'title_ar' => 'عرض خاص',
                'body_en' => 'Hello {{name}}, enjoy {{discount}} off!',
                'body_ar' => 'مرحباً {{name}}، استمتع بخصم {{discount}}!',
                'variables_schema' => json_encode(['name'=>'string','discount'=>'string']),
                'channel' => 'in_app',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'points_awarded',
                'type' => 'POINTS_AWARDED',
                'title_en' => 'Points Awarded',
                'title_ar' => 'نقاط تم منحها',
                'body_en' => 'You have earned {{points}} points for your activity.',
                'body_ar' => 'لقد ربحت {{points}} نقاط مقابل نشاطك.',
                'variables_schema' => json_encode(['points'=>'int']),
                'channel' => 'in_app',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($samples as $s) {
            $existing = DB::table('notification_templates')->where('key', $s['key'])->orWhere('type', $s['type'])->first();
            if ($existing) {
                DB::table('notification_templates')->where('id', $existing->id)->update($s);
            } else {
                DB::table('notification_templates')->insert($s);
            }
        }
    }
}
