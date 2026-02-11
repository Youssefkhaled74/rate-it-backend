<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoEgyptianBrandsBranchesSeeder extends Seeder
{
    public function run()
    {
        $subcategories = DB::table('subcategories')
            ->where('is_active', 1)
            ->get(['id', 'name_en']);

        if ($subcategories->isEmpty()) {
            $this->command?->warn('No active subcategories found. Seed subcategories first.');
            return;
        }

        $subByName = [];
        foreach ($subcategories as $sub) {
            $subByName[Str::lower((string) $sub->name_en)] = (int) $sub->id;
        }
        $defaultSubcategoryId = (int) $subcategories->first()->id;

        $cityIds = DB::table('cities')->where('is_active', 1)->pluck('id')->map(fn ($v) => (int) $v)->values()->all();
        $areasByCity = DB::table('areas')
            ->where('is_active', 1)
            ->get(['id', 'city_id'])
            ->groupBy('city_id')
            ->map(fn ($rows) => collect($rows)->pluck('id')->map(fn ($v) => (int) $v)->values()->all())
            ->toArray();

        $brands = $this->brandSeeds();

        foreach ($brands as $brandSeed) {
            $subcategoryId = $subByName[Str::lower($brandSeed['subcategory_name_en'])] ?? $defaultSubcategoryId;

            $brandId = $this->upsertBrand($brandSeed, $subcategoryId);
            $this->upsertBranches($brandSeed, $brandId, $cityIds, $areasByCity);
        }
    }

    private function upsertBrand(array $brandSeed, int $subcategoryId): int
    {
        $payload = [
            'name_en' => $brandSeed['name_en'],
            'name_ar' => $brandSeed['name_ar'],
            'subcategory_id' => $subcategoryId,
            'description_en' => $brandSeed['description_en'],
            'description_ar' => $brandSeed['description_ar'],
            'logo' => 'assets/images/Vector.png',
            'cover_image' => null,
            'is_active' => 1,
            'points_expiry_days' => 180,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addYear(),
            'updated_at' => now(),
        ];

        $existing = DB::table('brands')->where('name_en', $brandSeed['name_en'])->first();
        if ($existing) {
            DB::table('brands')->where('id', $existing->id)->update($payload + ['deleted_at' => null]);
            return (int) $existing->id;
        }

        return (int) DB::table('brands')->insertGetId($payload + ['created_at' => now()]);
    }

    private function upsertBranches(array $brandSeed, int $brandId, array $cityIds, array $areasByCity): void
    {
        foreach ($brandSeed['branches'] as $i => $branchName) {
            $cityId = $cityIds ? $cityIds[$i % count($cityIds)] : null;
            $areaIds = $cityId ? ($areasByCity[$cityId] ?? []) : [];
            $areaId = $areaIds ? $areaIds[array_rand($areaIds)] : null;

            $existing = DB::table('branches')
                ->where('brand_id', $brandId)
                ->where('name', $branchName)
                ->first();

            $payload = [
                'brand_id' => $brandId,
                'name' => $branchName,
                'logo' => 'assets/images/Vector.png',
                'cover_image' => null,
                'address' => $this->egyptianAddress($branchName),
                'lat' => null,
                'lng' => null,
                'working_hours' => json_encode([
                    'sun_thu' => '10:00-22:00',
                    'fri_sat' => '12:00-23:00',
                ]),
                'qr_generated_at' => now(),
                'review_cooldown_days' => 0,
                'is_active' => 1,
                'city_id' => $cityId,
                'area_id' => $areaId,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('branches')
                    ->where('id', $existing->id)
                    ->update($payload + ['deleted_at' => null]);
                continue;
            }

            $qr = $this->uniqueQrValue($brandSeed['name_en'], $i + 1);
            DB::table('branches')->insert($payload + [
                'qr_code_value' => $qr,
                'created_at' => now(),
            ]);
        }
    }

    private function uniqueQrValue(string $brandName, int $index): string
    {
        $base = 'EG-' . Str::upper(Str::substr(Str::slug($brandName, ''), 0, 8)) . '-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT);
        $value = $base;
        $suffix = 1;

        while (DB::table('branches')->where('qr_code_value', $value)->exists()) {
            $value = $base . '-' . $suffix;
            $suffix++;
        }

        return $value;
    }

    private function egyptianAddress(string $branchName): string
    {
        $streets = [
            'Tahrir Square',
            'Abbas El Akkad St.',
            'Makram Ebeid St.',
            'El Horreya Rd.',
            'Salah Salem St.',
            'Corniche El Nile',
            'Mostafa El Nahas St.',
        ];

        return $branchName . ' - ' . $streets[array_rand($streets)] . ', Egypt';
    }

    private function brandSeeds(): array
    {
        return [
            [
                'name_en' => 'Nile Style Fashion',
                'name_ar' => 'نايل ستايل فاشون',
                'subcategory_name_en' => 'Clothing',
                'description_en' => 'Egyptian fashion brand for daily and formal wear.',
                'description_ar' => 'علامة أزياء مصرية للملابس اليومية والرسمية.',
                'branches' => ['Nile Style - Nasr City', 'Nile Style - Maadi', 'Nile Style - Alexandria'],
            ],
            [
                'name_en' => 'Cairo Tech Hub',
                'name_ar' => 'كايرو تك هب',
                'subcategory_name_en' => 'Electronics',
                'description_en' => 'Electronics and smart devices with warranty support.',
                'description_ar' => 'إلكترونيات وأجهزة ذكية مع دعم وضمان.',
                'branches' => ['Cairo Tech - Heliopolis', 'Cairo Tech - Dokki', 'Cairo Tech - Mansoura'],
            ],
            [
                'name_en' => 'Golden Mall Stores',
                'name_ar' => 'جولدن مول ستورز',
                'subcategory_name_en' => 'Malls',
                'description_en' => 'Family-friendly shopping experience with trusted brands.',
                'description_ar' => 'تجربة تسوق عائلية مع علامات موثوقة.',
                'branches' => ['Golden Mall - New Cairo', 'Golden Mall - 6th October', 'Golden Mall - Alex'],
            ],
            [
                'name_en' => 'Shifa Plus Clinics',
                'name_ar' => 'شفا بلس كلينكس',
                'subcategory_name_en' => 'Clinics',
                'description_en' => 'Outpatient clinics with integrated booking.',
                'description_ar' => 'عيادات تخصصية مع حجز مواعيد متكامل.',
                'branches' => ['Shifa Plus - Nasr City', 'Shifa Plus - Mohandessin', 'Shifa Plus - Zagazig'],
            ],
            [
                'name_en' => 'El Amal Hospitals',
                'name_ar' => 'مستشفيات الأمل',
                'subcategory_name_en' => 'Hospitals',
                'description_en' => 'Hospital services with continuous care quality.',
                'description_ar' => 'خدمات مستشفى بجودة رعاية مستمرة.',
                'branches' => ['El Amal - Giza', 'El Amal - Tanta', 'El Amal - Alexandria'],
            ],
            [
                'name_en' => 'Pharma One Egypt',
                'name_ar' => 'فارما وان مصر',
                'subcategory_name_en' => 'Pharmacies',
                'description_en' => 'Community pharmacies with fast service.',
                'description_ar' => 'صيدليات مجتمعية بخدمة سريعة.',
                'branches' => ['Pharma One - Maadi', 'Pharma One - Heliopolis', 'Pharma One - Smouha'],
            ],
        ];
    }
}
