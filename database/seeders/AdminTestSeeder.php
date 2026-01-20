<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminTestSeeder extends Seeder
{
    /**
     * Seed the database with test data for Admin Feature tests.
     * This seeder is idempotent and isolated for test environments.
     */
    public function run()
    {
        // Create super admin user for tests
        $superAdmin = DB::table('admins')->updateOrInsert(
            ['email' => 'admin@test.local'],
            [
                'name' => 'Test Admin',
                'phone' => null,
                'password_hash' => Hash::make('password'),
                'role' => 'SUPER_ADMIN',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $superAdminRecord = DB::table('admins')->where('email', 'admin@test.local')->first();

        // Create test admin for permission checks
        $testAdmin = DB::table('admins')->updateOrInsert(
            ['email' => 'test.admin@test.local'],
            [
                'name' => 'Test Admin 2',
                'phone' => null,
                'password_hash' => Hash::make('password'),
                'role' => 'ADMIN',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create roles and permissions if not exist
        $this->ensureRolesAndPermissions();

        // Assign SUPER_ADMIN role to super admin
        $superAdminRole = DB::table('roles')->where('name', 'SUPER_ADMIN')->first();
        if ($superAdminRecord && $superAdminRole) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $superAdminRole->id,
                    'model_type' => \App\Models\Admin::class,
                    'model_id' => $superAdminRecord->id,
                ],
                []
            );
        }

        // Assign ADMIN role to test admin
        $adminRole = DB::table('roles')->where('name', 'ADMIN')->first();
        if ($testAdmin = DB::table('admins')->where('email', 'test.admin@test.local')->first()) {
            if ($adminRole) {
                DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => $adminRole->id,
                        'model_type' => \App\Models\Admin::class,
                        'model_id' => $testAdmin->id,
                    ],
                    []
                );
            }
        }

        // Seed minimum catalog data for tests
        $this->seedCatalogData();
    }

    private function ensureRolesAndPermissions()
    {
        $roles = [
            'SUPER_ADMIN' => 'Super Administrator',
            'ADMIN' => 'Administrator',
            'SUPPORT' => 'Support',
            'FINANCE' => 'Finance',
            'VENDOR_ADMIN' => 'Vendor Admin',
            'BRANCH_STAFF' => 'Branch Staff',
        ];

        $permissions = [
            // Master Data
            'master.categories.manage',
            'master.brands.manage',
            'master.places.manage',
            // Dashboard
            'dashboard.view',
            // Reviews
            'reviews.view',
            'reviews.moderate',
            // Points
            'points.view',
            'points.adjust',
            // Notifications
            'notifications.send',
            'notifications.manage_templates',
            // Subscriptions
            'subscriptions.view',
            'subscriptions.manage',
            // Vouchers
            'vouchers.view',
            'vouchers.redeem',
            'vouchers.manage',
            // Reports
            'reports.view',
            // RBAC
            'rbac.roles.manage',
            'rbac.permissions.manage',
        ];

        // Create permissions
        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['name' => $p], ['guard' => 'admin', 'description' => null, 'updated_at' => now(), 'created_at' => now()]);
        }

        // Create roles
        foreach ($roles as $key => $label) {
            DB::table('roles')->updateOrInsert(['name' => $key], ['guard' => 'admin', 'description' => $label, 'updated_at' => now(), 'created_at' => now()]);
        }

        // Assign all permissions to SUPER_ADMIN
        $superRole = DB::table('roles')->where('name', 'SUPER_ADMIN')->first();
        $allPermissions = DB::table('permissions')->pluck('id')->toArray();
        if ($superRole) {
            foreach ($allPermissions as $permId) {
                DB::table('role_has_permissions')->updateOrInsert(['role_id' => $superRole->id, 'permission_id' => $permId], []);
            }
        }
    }

    private function seedCatalogData()
    {
        // Seed Categories
        $category = DB::table('categories')->updateOrInsert(
            ['name_en' => 'Test Category'],
            [
                'name_ar' => 'فئة الاختبار',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $categoryId = DB::table('categories')->where('name_en', 'Test Category')->first()?->id;

        // Seed Subcategories
        if ($categoryId) {
            $subcategory = DB::table('subcategories')->updateOrInsert(
                ['name_en' => 'Test Subcategory', 'category_id' => $categoryId],
                [
                    'name_ar' => 'فئة فرعية للاختبار',
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Seed Brands
        $brand = DB::table('brands')->updateOrInsert(
            ['name_en' => 'Test Brand'],
            [
                'name_ar' => 'اختبار العلامة التجارية',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed Places
        $place = DB::table('places')->updateOrInsert(
            ['name_en' => 'Test Place'],
            [
                'name_ar' => 'مكان الاختبار',
                'is_active' => true,
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $placeId = DB::table('places')->where('name_en', 'Test Place')->first()?->id;
        $brandId = DB::table('brands')->where('name_en', 'Test Brand')->first()?->id;

        // Link Place to Brand (Task 02.1)
        if ($placeId && $brandId) {
            DB::table('place_brand')->updateOrInsert(
                ['place_id' => $placeId, 'brand_id' => $brandId],
                []
            );
        }

        // Link Place to Category/Subcategory (Task 02.2)
        if ($placeId && $categoryId) {
            DB::table('place_category')->updateOrInsert(
                ['place_id' => $placeId, 'category_id' => $categoryId],
                []
            );
        }

        if ($placeId) {
            $subcategoryId = DB::table('subcategories')->where('name_en', 'Test Subcategory')->first()?->id;
            if ($subcategoryId) {
                DB::table('place_subcategory')->updateOrInsert(
                    ['place_id' => $placeId, 'subcategory_id' => $subcategoryId],
                    []
                );
            }
        }

        // Seed Branches
        if ($placeId) {
            $branch = DB::table('branches')->updateOrInsert(
                ['name_en' => 'Test Branch', 'place_id' => $placeId],
                [
                    'name_ar' => 'فرع الاختبار',
                    'is_active' => true,
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                    'qr_code_value' => 'TEST_QR_' . time(),
                    'qr_generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Seed RatingCriteria
        $ratingCriteria = DB::table('rating_criteria')->updateOrInsert(
            ['name_en' => 'Test Criteria'],
            [
                'name_ar' => 'معايير الاختبار',
                'type' => 'RATING', // or MULTIPLE_CHOICE
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed RatingCriteriaChoices if type is MULTIPLE_CHOICE
        $criteriaId = DB::table('rating_criteria')->where('name_en', 'Test Criteria')->first()?->id;
        if ($criteriaId) {
            $choice1 = DB::table('rating_criteria_choices')->updateOrInsert(
                ['criteria_id' => $criteriaId, 'value' => 1],
                [
                    'name_en' => 'Choice 1',
                    'name_ar' => 'الخيار 1',
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $choice2 = DB::table('rating_criteria_choices')->updateOrInsert(
                ['criteria_id' => $criteriaId, 'value' => 2],
                [
                    'name_en' => 'Choice 2',
                    'name_ar' => 'الخيار 2',
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Link Subcategory to RatingCriteria (Task 01)
        if ($subcategoryId = DB::table('subcategories')->where('name_en', 'Test Subcategory')->first()?->id) {
            if ($criteriaId) {
                DB::table('subcategory_rating_criteria')->updateOrInsert(
                    ['subcategory_id' => $subcategoryId, 'criteria_id' => $criteriaId],
                    []
                );
            }
        }

        // Seed PointsSettings for Loyalty (Task 06)
        DB::table('points_settings')->updateOrInsert(
            ['id' => 1],
            [
                'per_review' => 10,
                'per_reply' => 5,
                'is_active' => true,
                'version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed SubscriptionPlans (Task 09)
        DB::table('subscription_plans')->updateOrInsert(
            ['name_en' => 'Test Plan'],
            [
                'name_ar' => 'خطة اختبار',
                'description_en' => 'Test subscription plan',
                'description_ar' => 'خطة الاشتراك في الاختبار',
                'amount' => 99.99,
                'currency' => 'SAR',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
