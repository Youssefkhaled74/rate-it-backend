<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FillBranchArabicNamesSeeder extends Seeder
{
    public function run(): void
    {
        $cityMap = $this->cityMap();
        $brandMap = $this->brandMap();

        DB::table('branches')
            ->leftJoin('brands', 'brands.id', '=', 'branches.brand_id')
            ->select([
                'branches.id',
                'branches.name',
                'branches.name_en',
                'branches.name_ar',
                'branches.brand_id',
                'brands.name_en as brand_name_en',
                'brands.name_ar as brand_name_ar',
            ])
            ->orderBy('branches.id')
            ->chunk(200, function ($rows) use ($cityMap, $brandMap): void {
                foreach ($rows as $row) {
                    $source = trim((string) ($row->name_en ?: $row->name ?: ''));
                    if ($source === '') {
                        continue;
                    }

                    $target = $this->buildArabicBranchName($source, (string) ($row->brand_name_en ?? ''), (string) ($row->brand_name_ar ?? ''), $brandMap, $cityMap);
                    if ($target === '') {
                        continue;
                    }

                    DB::table('branches')
                        ->where('id', $row->id)
                        ->update([
                            'name_ar' => $target,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    private function buildArabicBranchName(
        string $source,
        string $brandNameEn,
        string $brandNameAr,
        array $brandMap,
        array $cityMap
    ): string {
        $parts = array_map('trim', explode('-', $source, 2));
        $sourceBrand = trim($parts[0] ?? '');
        $sourceCity = trim($parts[1] ?? '');

        $brandAr = $this->resolveBrandArabic($sourceBrand, $brandNameEn, $brandNameAr, $brandMap);
        if ($sourceCity === '') {
            return $brandAr;
        }

        $cityAr = $cityMap[strtolower($sourceCity)] ?? $sourceCity;
        return $brandAr . ' - ' . $cityAr;
    }

    private function resolveBrandArabic(
        string $sourceBrand,
        string $brandNameEn,
        string $brandNameAr,
        array $brandMap
    ): string {
        $trimmedBrandAr = trim($brandNameAr);
        if ($trimmedBrandAr !== '') {
            return $trimmedBrandAr;
        }

        $key = strtolower(trim($sourceBrand !== '' ? $sourceBrand : $brandNameEn));
        return $brandMap[$key] ?? ($sourceBrand !== '' ? $sourceBrand : $brandNameEn);
    }

    private function cityMap(): array
    {
        return [
            'nasr city' => 'مدينة نصر',
            'maadi' => 'المعادي',
            'alexandria' => 'الإسكندرية',
            'alex' => 'الإسكندرية',
            'heliopolis' => 'مصر الجديدة',
            'dokki' => 'الدقي',
            'mansoura' => 'المنصورة',
            'new cairo' => 'القاهرة الجديدة',
            '6th october' => '6 أكتوبر',
            'mohandessin' => 'المهندسين',
            'zagazig' => 'الزقازيق',
            'giza' => 'الجيزة',
            'tanta' => 'طنطا',
            'smouha' => 'سموحة',
        ];
    }

    private function brandMap(): array
    {
        return [
            'nile style' => 'نايل ستايل',
            'nile style fashion' => 'نايل ستايل فاشون',
            'cairo tech' => 'كايرو تك',
            'cairo tech hub' => 'كايرو تك هب',
            'golden mall' => 'جولدن مول',
            'golden mall stores' => 'جولدن مول ستورز',
            'shifa plus' => 'شفا بلس',
            'shifa plus clinics' => 'شفا بلس كلينكس',
            'el amal' => 'الأمل',
            'el amal hospitals' => 'مستشفيات الأمل',
            'pharma one' => 'فارما وان',
            'pharma one egypt' => 'فارما وان مصر',
        ];
    }
}

