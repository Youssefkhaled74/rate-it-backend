# Vendor Module Implementation — Codebase Analysis & Preparation

**Date**: January 25, 2026  
**Project**: Rate-It Backend (Laravel 12 Modular Architecture)  
**Scope**: PROMPT 0 — READ CODEBASE & INITIALIZE (No coding)

---

## SECTION 1: WHAT ALREADY EXISTS

### 1.1 Project Structure & Modular Layout
```
app/Modules/
├── Admin/          [Super-admin management module]
│   ├── Auth/
│   ├── Catalog/    [Categories, Brands, Places, Branches, Rating Criteria]
│   ├── Dashboard/
│   ├── Invites/
│   ├── LoyaltySettings/
│   ├── Notifications/
│   ├── Points/
│   ├── Rbac/       [Role-Based Access Control]
│   ├── Reviews/
│   ├── Subscriptions/
│   └── Users/      [Regular user management by admin]
└── User/           [Public-facing, authenticated user module]
    ├── Auth/       [User login/register/OTP]
    ├── Brands/
    ├── Categories/
    ├── Home/
    ├── Invites/
    ├── Lookups/
    ├── Notifications/
    ├── Onboarding/
    ├── Points/
    ├── Profile/
    ├── Reviews/
    └── Subscriptions/
```

**Module Architecture Pattern**:
- Each module has: `Controllers/`, `Services/`, `Requests/`, `Resources/`, `Routes/`, sometimes `Repositories/`
- Route files organize endpoints with middleware: `app/Modules/{Module}/{Submodule}/Routes/api.php`
- Main entry point: `routes/api.php` includes all module routes under versioned prefix (`/api/v1/user`, `/api/v1/admin`)

### 1.2 Authentication & Authorization System

**Guards & Providers** ([config/auth.php](config/auth.php)):
- **`admin` guard**: Uses Sanctum driver with `App\Models\Admin` provider
  - Issued via `Admin::createToken()` (from `HasApiTokens`)
  - Validated by middleware: `AdminAuthenticate` (checks `Auth::guard('admin')->check()`)
  
- **User guard** (web/default): Uses Sanctum driver with `App\Models\User` provider
  - Sanctum tokens for mobile/API access
  - Activated via `auth:sanctum` middleware

**RBAC System** ([app/Modules/Admin/Rbac/](app/Modules/Admin/Rbac/)):
- Tables: `roles`, `permissions`, `role_has_permissions`, `model_has_roles`
- **Permission format**: kebab-case strings (e.g., `'dashboard.view'`, `'rbac.roles.manage'`)
- **Middleware**: `AdminPermission::class.':permission.name'` — checks `model_has_roles` → `role_has_permissions` → permission name
- **Models**: [Role.php](app/Models/Role.php), [Permission.php](app/Models/Permission.php) (not shown but inferred)
- **Existing roles**: `SUPER_ADMIN`, `ADMIN` (in `admins.role` enum)

### 1.3 Database Schema — Key Tables

#### Existing Vendor-Related Tables:

**vendor_users** ([2026_01_17_000070](database/migrations/2026_01_17_000070_create_vendor_users_table.php)):
```sql
- id (PK)
- brand_id (FK → brands, nullable, soft-delete on delete)
- branch_id (FK → branches, nullable, soft-delete on delete)
- name (string)
- phone (string, unique)
- email (string, nullable, unique)
- password_hash (string, nullable)
- role (enum: 'VENDOR_ADMIN', 'BRANCH_STAFF')
- is_active (boolean, default true)
- created_at, updated_at (TZ-aware)
- deleted_at (soft delete)
- Indexes: brand_id, branch_id
- Constraint (PostgreSQL): BRANCH_STAFF must have branch_id NOT NULL
```

**brands** ([2026_01_17_000040](database/migrations/2026_01_17_000040_create_brands_table.php)):
```sql
- id, name, logo_url, points_expiry_days
- created_at, updated_at (TZ-aware)
- deleted_at (soft delete)
```

**branches** ([2026_01_17_000060](database/migrations/2026_01_17_000060_create_branches_table.php)):
```sql
- id, place_id (FK, cascade)
- name (nullable), address, lat, lng
- working_hours (JSON)
- qr_code_value (string, unique)
- qr_generated_at (timestamp)
- review_cooldown_days (int, default 0)
- created_at, updated_at, deleted_at
- Index: place_id
```

**reviews** ([2026_01_17_000100](database/migrations/2026_01_17_000100_create_reviews_table.php)):
```sql
- id, user_id (FK), place_id (FK, nullable), branch_id (FK)
- overall_rating (decimal 2,1), comment (text)
- status (enum: 'ACTIVE', 'DELETED_BY_ADMIN')
- review_score (decimal 5,2)
- created_at, updated_at, deleted_at
- Indexes: user_id, branch_id, place_id, created_at
```

**vouchers** ([2026_01_17_000170](database/migrations/2026_01_17_000170_create_vouchers_table.php)):
```sql
- id, user_id (FK), brand_id (FK), code (unique), points_used (int)
- value_amount (decimal 10,2, nullable)
- status (enum: 'VALID', 'USED', 'EXPIRED')
- issued_at, expires_at, used_at (timestamps)
- used_branch_id (FK → branches, nullable)
- verified_by_vendor_user_id (FK → vendor_users, nullable)
- created_at, updated_at
- Indexes: [user_id, status], [brand_id, status]
```

**points_transactions** ([2026_01_17_000140](database/migrations/2026_01_17_000140_create_points_transactions_table.php)):
- Core for loyalty: `user_id`, `brand_id`, `amount`, `type`, `expires_at`, `meta` (JSON)
- Polymorphic: `reference_type`, `reference_id` (links to Review, Voucher, etc.)

**subscriptions** ([2026_01_17_000130](database/migrations/2026_01_17_000130_create_subscriptions_table.php)):
- User subscriptions to brands: `user_id`, `brand_id`, `subscription_plan_id`

### 1.4 Core Models & Relationships

- [User](app/Models/User.php): Sanctum tokens, gender/nationality relations
- [Admin](app/Models/Admin.php): Sanctum tokens, RBAC roles
- [VendorUser](app/Models/VendorUser.php): **Minimal** — only has `guarded = []`; needs relationships
- [Brand](app/Models/Brand.php): **Minimal** — no relationships defined
- [Place](app/Models/Place.php): `belongsTo(Subcategory)`, `belongsTo(Brand)`
- [Branch](app/Models/Branch.php): **Not shown** — assumed basic with `belongsTo(Place)`
- [Review](app/Models/Review.php): Relations to user, place, branch, answers, photos
- [PointsTransaction](app/Models/PointsTransaction.php): Relations to user, brand, polymorphic reference
- [Voucher](app/Models/Voucher.php): **Not shown** — has FK to user, brand, branch, vendor_user
- [Role](app/Models/Role.php): `belongsToMany(Permission)` via `role_has_permissions`

### 1.5 Response & Request Patterns

**Response Wrapper** ([ApiResponseTrait](app/Support/Traits/Api/ApiResponseTrait.php)):
```json
{
  "success": true|false,
  "message": "Translated message or null",
  "data": null|object|array,
  "meta": null|{
    "page": int,
    "limit": int,
    "total": int
  }
}
```

**Base Controller**: [BaseApiController](app/Support/Api/BaseApiController.php) extends Controller, uses `ApiResponseTrait`
- Methods: `success()`, `error()`, `created()`, `paginated()`, `noContent()`
- Messages use i18n keys, e.g., `'auth.login_success'`, `'ratings.list'`

**Form Requests**: Extend [BaseFormRequest](app/Support/Api/FormRequest/BaseFormRequest.php)
- Example: [StoreBranchRequest](app/Modules/Admin/Catalog/Requests/StoreBranchRequest.php)
- All use Laravel's native validation + translation keys

**Resources** (JSON serializers): Extend `JsonResource`
- Example: [BranchResource](app/Modules/Admin/Catalog/Resources/BranchResource.php)
- Structured fields, nested timestamps via `TimestampResource`

### 1.6 Service Layer Pattern

Example: [BranchService](app/Modules/Admin/Catalog/Services/BranchService.php)
- Constructor injection in controllers
- Methods: `list()`, `find()`, `create()`, `update()`, `delete()`
- Business logic (validation, unique checks) in service, throw `\RuntimeException` with i18n message keys
- Return models or null

### 1.7 Existing Routes

**Admin routes** ([routes/api.php](routes/api.php)):
- `POST /api/v1/admin/auth/login`
- `GET /api/v1/admin/auth/me` + `POST /logout` (auth:sanctum)
- `GET /api/v1/admin/roles`, `POST /roles`, `POST /roles/{id}/sync-permissions`
- `GET /api/v1/admin/permissions`
- Catalog: brands, places, branches, categories, rating criteria (CRUD)
- Dashboard: summary, top places, reviews chart
- Users: list, block/unblock
- Points, Loyalty Settings, Notifications, Invites, Subscriptions management
- All protected by `AdminAuthenticate` + `AdminPermission` middleware

**User routes** ([routes/api.php](routes/api.php)):
- Auth: login, register, OTP, phone verification, logout, me
- Brands/Places: show details, list reviews
- Categories: list, subcategories
- Reviews: submit, list
- Points: summary, history, redeem
- Invites, Notifications, Profile, Subscriptions, Lookups (genders, nationalities)
- Most protected by `auth:sanctum`

### 1.8 Naming Conventions & Patterns

- **Namespaces**: `App\Modules\{ModuleName}\{Submodule}\{EntityType}`
- **Middleware names**: `AdminAuthenticate`, `AdminPermission`
- **i18n keys**: kebab-case, hierarchical: `'auth.login_success'`, `'vouchers.redeemed'`
- **Enums in DB**: `ENUM('VALUE1','VALUE2')` — e.g., roles, statuses
- **Controller methods**: RESTful + action verbs: `index()`, `show()`, `store()`, `update()`, `delete()`, custom actions like `redeemVoucher()`
- **Soft deletes**: Used on `users`, `brands`, `admins`, `vendor_users`, `reviews`, `branches`
- **Timestamps**: All tables use `created_at`, `updated_at`, many also use `deleted_at` (TZ-aware)

---

## SECTION 2: WHAT IS MISSING FOR VENDOR LOGIC

### Gap Analysis Checklist:

- ❌ **Vendor Module folder** (`app/Modules/Vendor/`) — does NOT exist
  
- ❌ **Vendor Authentication**:
  - No Vendor Auth controller/service
  - No Vendor login/register routes
  - No Sanctum tokens for vendor users
  - No guard in `config/auth.php` for vendors

- ❌ **Vendor Relationships**:
  - `VendorUser` model has NO relationships to Brand, Branch, or Role
  - `Brand` model has NO relationship to Vendor Users
  - `Branch` model has NO relationship to Vendor Users

- ❌ **Vendor RBAC**:
  - No vendor-level permissions defined
  - No vendor roles (e.g., `'vendor.branches.manage'`, `'vendor.vouchers.verify'`)
  - Guard for vendor permissions not set (probably need `'vendor'` in roles.guard)

- ❌ **Vendor Dashboard/Reporting**:
  - No vendor summary/KPI endpoints
  - No voucher verification endpoints
  - No branch performance reporting

- ❌ **Vendor Voucher Verification**:
  - Vouchers table has `verified_by_vendor_user_id` FK, but no service to handle verification flow
  - No endpoint to scan/verify voucher codes

- ❌ **Vendor Profile & Settings**:
  - No profile management for vendor users
  - No settings/preferences API

- ❌ **Vendor Notifications**:
  - Migrations exist: `vendor_notifications` table ([2026_01_17_000200](database/migrations/2026_01_17_000200_create_vendor_notifications_table.php))
  - But no controller/service to handle vendor notifications

- ❌ **Vendor Middleware**:
  - No `VendorAuthenticate` middleware (equivalent to `AdminAuthenticate`)
  - No `VendorPermission` middleware (equivalent to `AdminPermission`)

- ❌ **i18n Keys for Vendor**:
  - No `vendor.*` translation keys; need to add

- ⚠️ **Service Layer**:
  - No Vendor service classes (BrandService, VoucherService for vendor context, etc.)

- ⚠️ **Resources**:
  - No Vendor API resources (e.g., VendorProfileResource, VoucherVerificationResource)

---

## SECTION 3: RECOMMENDED VENDOR MODULE PLACEMENT & ARCHITECTURE

### 3.1 Folder Structure (Proposed)

```
app/Modules/Vendor/
├── Auth/
│   ├── Controllers/
│   │   └── AuthController.php           [login, register, logout, me]
│   ├── Requests/
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   └── SendOtpRequest.php
│   ├── Resources/
│   │   └── VendorUserResource.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── OtpService.php
│   └── Routes/
│       └── api.php
│
├── Profile/
│   ├── Controllers/
│   │   └── ProfileController.php        [show, update]
│   ├── Requests/
│   │   └── UpdateProfileRequest.php
│   ├── Resources/
│   │   └── VendorProfileResource.php
│   ├── Services/
│   │   └── ProfileService.php
│   └── Routes/
│       └── api.php
│
├── Dashboard/
│   ├── Controllers/
│   │   └── DashboardController.php      [summary, revenue, voucher stats]
│   ├── Services/
│   │   └── DashboardService.php
│   └── Routes/
│       └── api.php
│
├── Vouchers/
│   ├── Controllers/
│   │   └── VoucherController.php        [list, verify/redeem, stats]
│   ├── Requests/
│   │   └── VerifyVoucherRequest.php
│   ├── Resources/
│   │   └── VoucherResource.php
│   ├── Services/
│   │   └── VoucherService.php
│   └── Routes/
│       └── api.php
│
├── Branches/
│   ├── Controllers/
│   │   └── BranchController.php         [list, show, update settings, QR]
│   ├── Requests/
│   │   └── UpdateBranchSettingsRequest.php
│   ├── Resources/
│   │   └── BranchDetailsResource.php
│   ├── Services/
│   │   └── BranchService.php
│   └── Routes/
│       └── api.php
│
├── Notifications/
│   ├── Controllers/
│   │   └── NotificationsController.php  [list, mark as read]
│   ├── Resources/
│   │   └── NotificationResource.php
│   ├── Services/
│   │   └── NotificationService.php
│   └── Routes/
│       └── api.php
│
└── Rbac/
    ├── Controllers/
    │   └── RolesController.php          [sync permissions, view]
    └── Routes/
        └── api.php
```

### 3.2 Route Prefix & Entry Point

Add to [routes/api.php](routes/api.php):
```php
Route::prefix('v1/vendor')->group(function () {
    require base_path('app/Modules/Vendor/Auth/Routes/api.php');
    require base_path('app/Modules/Vendor/Profile/Routes/api.php');
    require base_path('app/Modules/Vendor/Dashboard/Routes/api.php');
    require base_path('app/Modules/Vendor/Vouchers/Routes/api.php');
    require base_path('app/Modules/Vendor/Branches/Routes/api.php');
    require base_path('app/Modules/Vendor/Notifications/Routes/api.php');
    require base_path('app/Modules/Vendor/Rbac/Routes/api.php');
});
```

### 3.3 Authentication Guard & Middleware

Add to [config/auth.php](config/auth.php):
```php
'guards' => [
    // ... existing guards ...
    'vendor' => [
        'driver' => 'sanctum',
        'provider' => 'vendor_users',
    ],
],

'providers' => [
    // ... existing providers ...
    'vendor_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\VendorUser::class,
    ],
],
```

Create middleware:
- `app/Http/Middleware/VendorAuthenticate.php` (mirrors `AdminAuthenticate`)
- `app/Http/Middleware/VendorPermission.php` (mirrors `AdminPermission`, checks vendor roles/perms)

### 3.4 Vendor User Model Enhancement

Enhance [app/Models/VendorUser.php](app/Models/VendorUser.php):
```php
<?php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

class VendorUser extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'vendor_users';
    protected $fillable = ['brand_id', 'branch_id', 'name', 'phone', 'email', 'password_hash', 'role', 'is_active'];
    protected $hidden = ['password_hash'];
    protected $casts = ['is_active' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function roles() { return $this->morphToMany(Role::class, 'model', 'model_has_roles'); }

    public function verifyPassword(string $password): bool {
        return Hash::check($password, $this->password_hash);
    }
}
```

---

## SECTION 4: MINIMAL DATABASE CHANGES NEEDED

### Migration Checklist:

- ✅ **vendor_users** table — EXISTS (fully designed)
- ✅ **vendor_notifications** table — EXISTS (already in migrations)
- ✅ **roles**, **permissions**, **role_has_permissions**, **model_has_roles** — EXIST (generic RBAC)
- ✅ **vouchers** — EXISTS with `verified_by_vendor_user_id` FK

### Minimal New Migrations:

1. **Add `is_active` to `vendor_users` table** (if not already present)
   - ✅ Likely already present (from migration review)
   
2. **No new tables needed** — all schema exists
   
3. **Optional: Add migration to seed vendor roles/permissions**
   - Roles: `'VENDOR_ADMIN'`, `'BRANCH_STAFF'`
   - Permissions: `'vendor.dashboard.view'`, `'vendor.vouchers.verify'`, `'vendor.branches.manage'`, `'vendor.profile.manage'`, etc.
   - This can be a seeder or optional migration

---

## SECTION 5: STEP-BY-STEP IMPLEMENTATION PLAN

### Phase 1: Foundation (Prompts 1-2)
1. **PROMPT 1**: Create Vendor Module folder structure + base files
   - Create directories
   - Create base VendorUser model with relationships
   - Create base controller + middleware
   - Update config/auth.php for vendor guard

2. **PROMPT 2**: Vendor Authentication (Auth Module)
   - VendorAuthController, Requests, Resources
   - AuthService, OtpService (phone-based like User)
   - Auth routes (login, register, logout, me)
   - Sanctum token generation

### Phase 2: Core Features (Prompts 3-5)
3. **PROMPT 3**: Vendor Profile Management
   - ProfileController, Service, Resource
   - Profile routes (show, update name/email/etc.)

4. **PROMPT 4**: Voucher Verification & Management
   - VoucherController, Service, Resource
   - Verify/redeem endpoint (mark as USED, set verified_by_vendor_user_id)
   - List vouchers for brand (if VENDOR_ADMIN) or branch (if BRANCH_STAFF)
   - Voucher stats

5. **PROMPT 5**: Branch Management (Vendor perspective)
   - BranchController, Service, Resource (for vendor context)
   - List branches, view details, update settings (working hours, cooldown)
   - QR code display

### Phase 3: Dashboard & Reporting (Prompts 6-7)
6. **PROMPT 6**: Vendor Dashboard
   - DashboardController, Service
   - Summary KPIs (vouchers issued/verified, branch performance, recent activity)
   - Revenue/earnings (if applicable)

7. **PROMPT 7**: Vendor Notifications
   - NotificationsController, Service, Resource
   - List notifications, mark as read
   - Note: `vendor_notifications` table already exists

### Phase 4: RBAC & Polish (Prompts 8-9)
8. **PROMPT 8**: Vendor RBAC
   - RolesController (list roles, sync permissions)
   - Permission seeding (initial roles & permissions)
   - Middleware integration

9. **PROMPT 9**: Testing & Documentation
   - Postman collection or API docs
   - Error handling, edge cases
   - Final validation

---

## SECTION 6: KEY DECISION POINTS & ASSUMPTIONS

1. **Authentication Method**: Using **phone-based OTP** (like User module) + Sanctum tokens
   - Assumes vendor users prefer phone login (consistent with app design)

2. **Role Hierarchy**: Two vendor roles
   - `VENDOR_ADMIN`: Full access to brand (all branches)
   - `BRANCH_STAFF`: Access to single branch only
   - Enforced via middleware + service-level filtering

3. **Voucher Verification Flow**:
   - Vendor scans voucher code (QR or manual input)
   - Vendor verifies → system marks `status = 'USED'`, `verified_by_vendor_user_id = auth_id`, `used_at = now()`, `used_branch_id = vendor_branch_id`
   - Points are likely already in PointsTransaction (user balance is read-only from vendor perspective)

4. **Permission Guard**: Vendor roles use `guard = 'vendor'` (separate from Admin)
   - Keeps admin and vendor permission spaces clean

5. **Notification Type**: `vendor_notifications` table — used for vendor-specific alerts (e.g., "new voucher redeem request", "branch stats updated")

6. **No Separate Loyalty Logic**: Vendor module read-only on points/loyalty
   - Points awarded by admin system or user module
   - Vendor only sees reports

---

## SECTION 7: FILE PATHS REFERENCE (What Exists)

### Core Models
- [app/Models/User.php](app/Models/User.php)
- [app/Models/Admin.php](app/Models/Admin.php)
- [app/Models/VendorUser.php](app/Models/VendorUser.php) ← Needs relationships
- [app/Models/Brand.php](app/Models/Brand.php) ← Minimal
- [app/Models/Place.php](app/Models/Place.php)
- [app/Models/Review.php](app/Models/Review.php)
- [app/Models/PointsTransaction.php](app/Models/PointsTransaction.php)
- [app/Models/Voucher.php](app/Models/Voucher.php)
- [app/Models/Role.php](app/Models/Role.php)
- [app/Models/Permission.php](app/Models/Permission.php) (implied)

### Config
- [config/auth.php](config/auth.php) ← Add vendor guard/provider

### Middleware
- [app/Http/Middleware/AdminAuthenticate.php](app/Http/Middleware/AdminAuthenticate.php) ← Model for VendorAuthenticate
- [app/Http/Middleware/AdminPermission.php](app/Http/Middleware/AdminPermission.php) ← Model for VendorPermission

### Base Classes
- [app/Support/Api/BaseApiController.php](app/Support/Api/BaseApiController.php)
- [app/Support/Api/FormRequest/BaseFormRequest.php](app/Support/Api/FormRequest/BaseFormRequest.php)
- [app/Support/Traits/Api/ApiResponseTrait.php](app/Support/Traits/Api/ApiResponseTrait.php)

### Examples (Patterns to Follow)
- **Admin Module**:
  - [app/Modules/Admin/Auth/](app/Modules/Admin/Auth/) — Auth pattern
  - [app/Modules/Admin/Catalog/](app/Modules/Admin/Catalog/) — CRUD pattern
  - [app/Modules/Admin/Dashboard/](app/Modules/Admin/Dashboard/) — Reporting pattern
  
- **User Module**:
  - [app/Modules/User/Auth/](app/Modules/User/Auth/) — User auth pattern
  - [app/Modules/User/Brands/](app/Modules/User/Brands/) — Data read pattern
  - [app/Modules/User/Points/](app/Modules/User/Points/) — Loyalty read pattern

### Routes
- [routes/api.php](routes/api.php) ← Entry point for all module routes

### Migrations (Relevant)
- [database/migrations/2026_01_17_000070_create_vendor_users_table.php](database/migrations/2026_01_17_000070_create_vendor_users_table.php)
- [database/migrations/2026_01_17_000170_create_vouchers_table.php](database/migrations/2026_01_17_000170_create_vouchers_table.php)
- [database/migrations/2026_01_17_000200_create_vendor_notifications_table.php](database/migrations/2026_01_17_000200_create_vendor_notifications_table.php)
- [database/migrations/2026_01_19_200000_create_roles_and_permissions_tables.php](database/migrations/2026_01_19_200000_create_roles_and_permissions_tables.php)

---

## SUMMARY

✅ **What Exists**: Solid modular foundation, RBAC system, Auth patterns (User & Admin), schema for vendor users + vouchers.

❌ **What's Missing**: Vendor module folder, authentication flow, endpoints, middleware, relationships in models.

📋 **Plan**: 9-step implementation (phases 1-4) following existing patterns, minimal DB changes (none critical).

**Ready to proceed?** Create VENDOR_MODULE_PREP.md (this file) and start PROMPT 1.

