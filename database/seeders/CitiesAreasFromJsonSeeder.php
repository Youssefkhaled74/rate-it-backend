<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CitiesAreasFromJsonSeeder extends Seeder
{
    public function run(): void
    {
        $govPath = base_path('database/data/governorates.json');
        $areasPath = base_path('database/data/city_centers.json');

        $governorates = json_decode((string) file_get_contents($govPath), true);
        $cityCenters = json_decode((string) file_get_contents($areasPath), true);

        if (!is_array($governorates)) {
            throw new \RuntimeException('Invalid JSON: database/data/governorates.json');
        }

        if (!is_array($cityCenters)) {
            throw new \RuntimeException('Invalid JSON: database/data/city_centers.json');
        }

        $now = Carbon::now();

        // Avoid dangling references when replacing all cities/areas.
        if (Schema::hasTable('users')) {
            DB::table('users')->update(['city_id' => null, 'area_id' => null]);
        }
        if (Schema::hasTable('branches')) {
            DB::table('branches')->update(['city_id' => null, 'area_id' => null]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::table('areas')->truncate();
            DB::table('cities')->truncate();

            $citiesRows = [];
            $validCityIds = [];

            foreach ($governorates as $row) {
                if (!isset($row['id'], $row['name_en'])) {
                    continue;
                }

                $cityId = (int) $row['id'];
                if ($cityId <= 0) {
                    continue;
                }

                $citiesRows[] = [
                    'id' => $cityId,
                    'name_en' => (string) ($row['name_en'] ?? ''),
                    'name_ar' => isset($row['name_ar']) ? (string) $row['name_ar'] : null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $validCityIds[$cityId] = true;
            }

            if (!empty($citiesRows)) {
                DB::table('cities')->insert($citiesRows);
            }

            $areasRows = [];
            foreach ($cityCenters as $row) {
                if (!isset($row['id'], $row['governorate_id'])) {
                    continue;
                }

                $cityId = (int) $row['governorate_id'];
                if (!isset($validCityIds[$cityId])) {
                    continue;
                }

                $areaId = (int) $row['id'];
                if ($areaId <= 0) {
                    continue;
                }

                $areasRows[] = [
                    'id' => $areaId,
                    'city_id' => $cityId,
                    'name_en' => (string) ($row['name_en'] ?? ''),
                    'name_ar' => isset($row['name_ar']) ? (string) $row['name_ar'] : null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($areasRows, 500) as $chunk) {
                DB::table('areas')->insert($chunk);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
