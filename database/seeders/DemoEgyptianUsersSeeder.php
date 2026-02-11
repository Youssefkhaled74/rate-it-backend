<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DemoEgyptianUsersSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ar_EG');
        $rows = [];
        $now = now();

        $hasName = Schema::hasColumn('users', 'name');
        $hasFirst = Schema::hasColumn('users', 'first_name');
        $hasLast = Schema::hasColumn('users', 'last_name');
        $hasPassword = Schema::hasColumn('users', 'password');
        $hasPasswordHash = Schema::hasColumn('users', 'password_hash');
        $hasRegisteredAt = Schema::hasColumn('users', 'registered_at');
        $hasIsActive = Schema::hasColumn('users', 'is_active');
        $hasIsBlocked = Schema::hasColumn('users', 'is_blocked');
        $hasNickname = Schema::hasColumn('users', 'nickname');
        $hasNationalityId = Schema::hasColumn('users', 'nationality_id');
        $hasGenderId = Schema::hasColumn('users', 'gender_id');
        $hasCityId = Schema::hasColumn('users', 'city_id');
        $hasAreaId = Schema::hasColumn('users', 'area_id');

        $genderIds = $hasGenderId ? DB::table('genders')->pluck('id')->toArray() : [];
        $nationalityIds = $hasNationalityId ? DB::table('nationalities')->pluck('id')->toArray() : [];
        $cityIds = $hasCityId ? DB::table('cities')->pluck('id')->toArray() : [];
        $areaIds = $hasAreaId ? DB::table('areas')->pluck('id')->toArray() : [];

        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['male', 'female', 'other']);
            $first = $faker->firstName($gender === 'male' ? 'male' : ($gender === 'female' ? 'female' : null));
            $last = $faker->lastName;
            $phone = '01' . str_pad((string) ($i + 100000000), 9, '0', STR_PAD_LEFT);
            $email = Str::slug($first . '.' . $last, '.') . ($i + 1) . '@example.eg';

            $row = [
                'phone' => $phone,
                'email' => $email,
                'birth_date' => Carbon::now()->subYears(rand(18, 55))->subDays(rand(0, 365)),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($hasName) $row['name'] = trim($first . ' ' . $last);
            if ($hasFirst) $row['first_name'] = $first;
            if ($hasLast) $row['last_name'] = $last;
            if ($hasNickname) $row['nickname'] = $first;
            if ($hasPassword) $row['password'] = Hash::make('12345678');
            if ($hasPasswordHash) $row['password_hash'] = null;
            if ($hasRegisteredAt) $row['registered_at'] = $now->copy()->subDays(rand(0, 365));
            if ($hasIsActive) $row['is_active'] = true;
            if ($hasIsBlocked) $row['is_blocked'] = false;
            if ($hasNationalityId) $row['nationality_id'] = $nationalityIds ? $faker->randomElement($nationalityIds) : null;
            if ($hasGenderId) $row['gender_id'] = $genderIds ? $faker->randomElement($genderIds) : null;
            if ($hasCityId) $row['city_id'] = $cityIds ? $faker->randomElement($cityIds) : null;
            if ($hasAreaId) $row['area_id'] = $areaIds ? $faker->randomElement($areaIds) : null;

            $rows[] = $row;
        }

        DB::table('users')->insert($rows);
    }
}
