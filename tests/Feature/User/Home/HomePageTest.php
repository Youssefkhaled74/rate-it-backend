<?php

namespace Tests\Feature\User\Home;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Place;
use App\Models\Review;
use App\Models\User;
use App\Modules\User\Home\Models\HomeBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_categories_banners_and_paginated_top_brands(): void
    {
        Category::create([
            'name_en' => 'Hospitals',
            'name_ar' => 'Hospitals',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        Category::create([
            'name_en' => 'Restaurants',
            'name_ar' => 'Restaurants',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Category::create([
            'name_en' => 'Inactive',
            'name_ar' => 'Inactive',
            'is_active' => false,
            'sort_order' => 3,
        ]);

        HomeBanner::create([
            'title_en' => 'Banner 1',
            'title_ar' => 'Banner 1',
            'body_en' => 'Body',
            'body_ar' => 'Body',
            'image' => 'assets/images/banner-1.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        HomeBanner::create([
            'title_en' => 'Banner 2',
            'title_ar' => 'Banner 2',
            'body_en' => 'Body',
            'body_ar' => 'Body',
            'image' => 'assets/images/banner-2.png',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        HomeBanner::create([
            'title_en' => 'Hidden Banner',
            'title_ar' => 'Hidden Banner',
            'body_en' => 'Body',
            'body_ar' => 'Body',
            'image' => 'assets/images/banner-3.png',
            'is_active' => false,
            'sort_order' => 3,
        ]);

        $topBrand = Brand::factory()->create([
            'name' => 'Top Brand',
            'name_en' => 'Top Brand',
            'name_ar' => 'Top Brand',
            'is_active' => true,
        ]);
        $secondBrand = Brand::factory()->create([
            'name' => 'Second Brand',
            'name_en' => 'Second Brand',
            'name_ar' => 'Second Brand',
            'is_active' => true,
        ]);

        $topPlace = Place::factory()->create([
            'brand_id' => $topBrand->id,
            'name' => 'Top Place',
            'is_active' => true,
        ]);
        $secondPlace = Place::factory()->create([
            'brand_id' => $secondBrand->id,
            'name' => 'Second Place',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $topBranch = Branch::create([
            'brand_id' => $topBrand->id,
            'name' => 'Top Branch',
            'address' => 'Address 1',
            'qr_code_value' => Str::upper(Str::random(12)),
            'qr_generated_at' => now(),
            'review_cooldown_days' => 0,
            'is_active' => true,
        ]);
        $secondBranch = Branch::create([
            'brand_id' => $secondBrand->id,
            'name' => 'Second Branch',
            'address' => 'Address 2',
            'qr_code_value' => Str::upper(Str::random(12)),
            'qr_generated_at' => now(),
            'review_cooldown_days' => 0,
            'is_active' => true,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'place_id' => $topPlace->id,
            'branch_id' => $topBranch->id,
            'overall_rating' => 4.8,
            'review_score' => 95,
            'status' => 'ACTIVE',
            'is_hidden' => false,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'place_id' => $secondPlace->id,
            'branch_id' => $secondBranch->id,
            'overall_rating' => 4.0,
            'review_score' => 80,
            'status' => 'ACTIVE',
            'is_hidden' => false,
        ]);

        $response = $this->getJson('/api/v1/user/home?per_page=1');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.top_brands.pagination.total', 2)
            ->assertJsonPath('data.top_brands.pagination.per_page', 1)
            ->assertJsonPath('data.top_brands.items.0.name', 'Top Brand');

        $this->assertCount(2, $response->json('data.categories'));
        $this->assertCount(2, $response->json('data.banners'));
    }
}
