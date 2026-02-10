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
            'description_en' => 'Flexible plan billed every month.',
            'description_ar' => 'خطة مرنة تُحاسب شهريًا.',
            'price_cents' => 9900,
            'currency' => 'USD',
            'interval' => 'month',
            'interval_count' => 1,
            'trial_days' => 30,
            'is_best_value' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        SubscriptionPlan::updateOrCreate(['code' => 'quarterly'], [
            'name_en' => 'Quarterly',
            'name_ar' => 'ربع سنوي',
            'description_en' => 'Save more with 3-month billing.',
            'description_ar' => 'وفر أكثر مع خطة 3 أشهر.',
            'price_cents' => 27000,
            'currency' => 'USD',
            'interval' => 'month',
            'interval_count' => 3,
            'trial_days' => 30,
            'is_best_value' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        SubscriptionPlan::updateOrCreate(['code' => 'semiannual'], [
            'name_en' => 'Semiannual',
            'name_ar' => 'نصف سنوي',
            'description_en' => 'Best for teams who want a 6-month cycle.',
            'description_ar' => 'مناسب للفرق التي تفضل دورة 6 أشهر.',
            'price_cents' => 49900,
            'currency' => 'USD',
            'interval' => 'month',
            'interval_count' => 6,
            'trial_days' => 45,
            'is_best_value' => false,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        SubscriptionPlan::updateOrCreate(['code' => 'annual'], [
            'name_en' => 'Annual',
            'name_ar' => 'سنوي',
            'description_en' => 'Best value for long-term partners.',
            'description_ar' => 'أفضل قيمة للشركاء على المدى الطويل.',
            'price_cents' => 99900,
            'currency' => 'USD',
            'interval' => 'year',
            'interval_count' => 1,
            'trial_days' => 60,
            'is_best_value' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::updateOrCreate(['code' => 'biennial'], [
            'name_en' => 'Biennial',
            'name_ar' => 'كل سنتين',
            'description_en' => 'Two-year billing for maximum savings.',
            'description_ar' => 'خطة لمدة سنتين لأقصى توفير.',
            'price_cents' => 179900,
            'currency' => 'USD',
            'interval' => 'year',
            'interval_count' => 2,
            'trial_days' => 60,
            'is_best_value' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
