<?php

namespace Tests\Feature\User\Auth\Support;

use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class UserAuthTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createLookups(): array
    {
        $gender = Gender::create([
            'code' => 'male',
            'name_en' => 'Male',
            'name_ar' => 'ذكر',
            'is_active' => true,
        ]);

        $nationality = Nationality::create([
            'country_code' => 'EG',
            'name_en' => 'Egyptian',
            'name_ar' => 'مصري',
            'is_active' => true,
        ]);

        $city = City::create([
            'name_en' => 'Cairo',
            'name_ar' => 'القاهرة',
            'is_active' => true,
        ]);

        $area = Area::create([
            'city_id' => $city->id,
            'name_en' => 'Nasr City',
            'name_ar' => 'مدينة نصر',
            'is_active' => true,
        ]);

        return compact('gender', 'nationality', 'city', 'area');
    }

    protected function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Test User',
            'phone' => '+201000000000',
            'email' => 'user@test.local',
            'password' => 'Password123!',
            'gender_id' => null,
            'nationality_id' => null,
            'city_id' => null,
            'area_id' => null,
        ], $overrides));
    }

    protected function authHeaders(string $token): array
    {
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }
}

