<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    public function run()
    {
        SubscriptionPlan::updateOrCreate(['code' => 'monthly'], [
            'name_en' => 'Monthly',
            'name_ar' => 'شهري',
            'description_en' => 'First 6 month free - Then $99/Month',
            'description_ar' => 'ستة أشهر مجانية ثم 99$/شهريًا',
            'price_cents' => 9900,
            'currency' => 'USD',
            'interval' => 'month',
            'interval_count' => 1,
            'trial_days' => 180,
            'is_best_value' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        SubscriptionPlan::updateOrCreate(['code' => 'annual'], [
            'name_en' => 'Annual',
            'name_ar' => 'سنوي',
            'description_en' => 'First 6 month free - Then $999/Year (Best Value)',
            'description_ar' => 'ستة أشهر مجانية ثم 999$/سنة (أفضل قيمة)',
            'price_cents' => 99900,
            'currency' => 'USD',
            'interval' => 'year',
            'interval_count' => 1,
            'trial_days' => 180,
            'is_best_value' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
