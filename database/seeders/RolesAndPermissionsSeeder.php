<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
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
            // Reviews
            'reviews.view', 'reviews.moderate',
            // Points
            'points.view', 'points.adjust',
            // Notifications
            'notifications.send', 'notifications.manage_templates',
            // Subscriptions
            'subscriptions.view', 'subscriptions.manage',
            // Vouchers
            'vouchers.view', 'vouchers.redeem', 'vouchers.manage',
            // Reports
            'reports.view',
            // RBAC
            'rbac.roles.manage', 'rbac.permissions.manage'
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['name' => $p], ['guard' => 'admin', 'description' => null, 'updated_at' => now(), 'created_at' => now()]);
        }

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

        // Attach SUPER_ADMIN role to default admin user(s)
        $admin = DB::table('admins')->where('email', 'admin@example.com')->first();
        if ($admin && $superRole) {
            DB::table('model_has_roles')->updateOrInsert([
                'role_id' => $superRole->id,
                'model_type' => \App\Models\Admin::class,
                'model_id' => $admin->id,
            ], []);
        }
    }
}
