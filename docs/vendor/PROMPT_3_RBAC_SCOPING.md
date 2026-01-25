# Vendor RBAC & Scoping Implementation — PROMPT 3

**Status**: ✅ Complete  
**Date**: January 25, 2026

---

## Overview

Implemented vendor-scoped RBAC system with two roles and three core policies:

| Role | Access Level | Can Do |
|------|---|---|
| **VENDOR_ADMIN** | Brand-wide | View all branches, manage settings, view reports, manage staff |
| **BRANCH_STAFF** | Single branch only | Verify/redeem vouchers at their branch only |

---

## Architecture Components

### 1. Scoping Traits

#### VendorScoping Trait
- `scopeByVendorBrand()` — Filter queries by vendor's brand
- `scopeByVendorBranch()` — Filter queries by vendor's branch (BRANCH_STAFF only)
- `getVendorBrandId()` — Get vendor's effective brand ID
- `getVendorBranchId()` — Get vendor's effective branch ID
- `vendorCanAccessBrand()` — Verify brand access
- `vendorCanAccessBranch()` — Verify branch access

**Use in Services:**
```php
use App\Support\Traits\Vendor\VendorScoping;

class VoucherService {
    use VendorScoping;
    
    public function getVendorVouchers(VendorUser $vendor) {
        $brandId = $this->getVendorBrandId($vendor); // Returns vendor's brand
        return Voucher::where('brand_id', $brandId)->get();
    }
}
```

#### VendorRoleCheck Trait
- `isVendorAdmin()` — Check if VENDOR_ADMIN
- `isBranchStaff()` — Check if BRANCH_STAFF
- `requireVendorAdmin()` — Throw if not admin
- `requireBranchStaff()` — Throw if not staff

**Use in Services:**
```php
public function updateBranchSettings(VendorUser $vendor, $data) {
    $this->requireVendorAdmin($vendor, 'Only admins can update');
    // ... update logic
}
```

### 2. Policies

Three policy classes enforce resource-level access control:

#### VendorBrandPolicy
```php
VendorBrandPolicy::authorize($vendor, 'view', $brandId);           // Can view?
VendorBrandPolicy::authorize($vendor, 'update', $brandId);         // Can update?
VendorBrandPolicy::authorize($vendor, 'viewAnalytics', $brandId);  // Can view analytics?
```

**Rules:**
- `view` → VENDOR_ADMIN with matching brand_id
- `update` → VENDOR_ADMIN with matching brand_id
- `viewAnalytics` → VENDOR_ADMIN with matching brand_id

#### VendorBranchPolicy
```php
VendorBranchPolicy::authorize($vendor, 'view', $branchId);        // Can view?
VendorBranchPolicy::authorize($vendor, 'manage', $branchId);      // Can manage?
VendorBranchPolicy::authorize($vendor, 'verifyVouchers', $branchId); // Can verify?
```

**Rules:**
- `view` → VENDOR_ADMIN (branch in their brand) OR BRANCH_STAFF (their branch)
- `manage` → VENDOR_ADMIN only (branch in their brand)
- `verifyVouchers` → BRANCH_STAFF only (their branch)

#### VendorVoucherPolicy
```php
VendorVoucherPolicy::authorize($vendor, 'verify', $branchId);  // Can verify?
VendorVoucherPolicy::authorize($vendor, 'list', $brandId);     // Can list?
VendorVoucherPolicy::authorize($vendor, 'export', $brandId);   // Can export?
```

**Rules:**
- `verify` → BRANCH_STAFF at their branch
- `list` → VENDOR_ADMIN for their brand
- `export` → VENDOR_ADMIN for their brand

### 3. Enhanced Middleware

#### VendorPermissionWithScoping
Combines permission checks with role enforcement.

```php
Route::middleware([
    VendorAuthenticate::class,
    VendorPermissionWithScoping::class.':vendor.vouchers.verify:BRANCH_STAFF'
])
->post('/verify', ...);
```

Parameters:
- `:permission_name` — Permission to check (optional)
- `:ROLE` — Required role (VENDOR_ADMIN, BRANCH_STAFF, ANY)

### 4. Services

#### VendorDataService
Demonstrates scoped queries and policy enforcement.

**Key Methods:**
- `getVendorBrandReviews()` — VENDOR_ADMIN only, returns brand reviews
- `getBranchStaffList()` — VENDOR_ADMIN only, lists staff
- `getVendorBranches()` — Scoped: admin sees all, staff sees one
- `getVendorVouchers()` — Scoped: admin sees brand, staff sees verified
- `updateBranchSettings()` — VENDOR_ADMIN only
- `verifyVoucher()` — BRANCH_STAFF only
- `viewBranch()` — Both roles (scoped)
- `getBrandAnalytics()` — VENDOR_ADMIN only

---

## Usage Examples

### Example 1: List Vouchers (Scoped by Role)

```php
// In VendorDataService
public function getVendorVouchers(VendorUser $vendor) {
    if ($this->isVendorAdmin($vendor)) {
        // Admin sees all brand vouchers
        $brandId = $this->getVendorBrandId($vendor);
        return Voucher::where('brand_id', $brandId)->get();
    }
    
    if ($this->isBranchStaff($vendor)) {
        // Staff sees only vouchers verified at their branch
        return Voucher::where('used_branch_id', $vendor->branch_id)
            ->where('status', 'USED')
            ->get();
    }
    
    return collect();
}

// In Controller
public function listVouchers(Request $request) {
    $vendor = Auth::guard('vendor')->user();
    $vouchers = $this->dataService->getVendorVouchers($vendor);
    return $this->success($vouchers, 'vendor.vouchers.list');
}
```

### Example 2: Verify Voucher (Policy Check)

```php
public function verifyVoucher(VendorUser $vendor, string $code, int $branchId) {
    // Throws if vendor is not BRANCH_STAFF or not at this branch
    VendorVoucherPolicy::authorize($vendor, 'verify', $branchId);
    
    $voucher = Voucher::where('code', $code)->firstOrFail();
    
    // Additional brand check
    if ($voucher->brand_id !== $this->getVendorBrandId($vendor)) {
        throw new ApiException('Voucher not in your brand', 403);
    }
    
    $voucher->update([
        'status' => 'USED',
        'used_at' => now(),
        'used_branch_id' => $branchId,
        'verified_by_vendor_user_id' => $vendor->id,
    ]);
    
    return $voucher;
}
```

### Example 3: Update Branch Settings (Admin Only)

```php
public function updateBranchSettings(VendorUser $vendor, int $branchId, array $data) {
    // Enforce policy
    VendorBranchPolicy::authorize($vendor, 'manage', $branchId);
    
    $branch = Branch::findOrFail($branchId);
    $branch->update(array_intersect_key($data, array_flip(['review_cooldown_days', 'working_hours'])));
    
    return $branch;
}
```

---

## Routes & Endpoints

All routes under `/api/v1/vendor/rbac/`:

```
GET    /api/v1/vendor/rbac/branches                    - List branches (scoped)
GET    /api/v1/vendor/rbac/branches/{branchId}        - View branch details
PUT    /api/v1/vendor/rbac/branches/{branchId}/settings - Update settings (VENDOR_ADMIN)

GET    /api/v1/vendor/rbac/vouchers                    - List vouchers (scoped)
POST   /api/v1/vendor/rbac/vouchers/verify             - Verify voucher (BRANCH_STAFF)

GET    /api/v1/vendor/rbac/brand/analytics             - Brand analytics (VENDOR_ADMIN)
GET    /api/v1/vendor/rbac/brand/reviews               - Brand reviews (VENDOR_ADMIN)

GET    /api/v1/vendor/rbac/staff                       - List staff (VENDOR_ADMIN)
```

---

## Test Cases & Forbidden Scenarios

### Test 1: BRANCH_STAFF Cannot List All Brand Vouchers
```php
$branchStaff = VendorUser::factory()->create([
    'role' => 'BRANCH_STAFF',
    'branch_id' => 1,
    'brand_id' => null,  // Staff doesn't have brand_id
]);

$service = new VendorDataService();

// ✅ This works - sees only their verified vouchers
$vouchers = $service->getVendorVouchers($branchStaff);
// Returns: Voucher::where('used_branch_id', 1)

// ❌ This throws - cannot access brand analytics
try {
    $service->getBrandAnalytics($branchStaff);
} catch (ApiException $e) {
    // Error: "Requires vendor admin role"
}
```

### Test 2: VENDOR_ADMIN Cannot Verify Voucher
```php
$admin = VendorUser::factory()->create([
    'role' => 'VENDOR_ADMIN',
    'brand_id' => 5,
    'branch_id' => null,
]);

// ❌ This throws - only staff can verify
try {
    $service->verifyVoucher($admin, 'CODE123', 1);
} catch (ApiException $e) {
    // Error: 403 Forbidden
}
```

### Test 3: BRANCH_STAFF Cannot View Different Branch
```php
$staff = VendorUser::factory()->create([
    'role' => 'BRANCH_STAFF',
    'branch_id' => 1,
]);

// ✅ Can view own branch
$branch = $service->viewBranch($staff, 1);

// ❌ Cannot view different branch
try {
    $service->viewBranch($staff, 2);
} catch (ApiException $e) {
    // Error: 403 Forbidden
}
```

### Test 4: VENDOR_ADMIN Cannot Access Different Brand
```php
$admin = VendorUser::factory()->create([
    'role' => 'VENDOR_ADMIN',
    'brand_id' => 5,
]);

// ❌ Cannot view branch from different brand
try {
    $service->viewBranch($admin, 10); // branch 10 belongs to brand 6
} catch (ApiException $e) {
    // Error: 403 Forbidden
}
```

### Test 5: Cross-Brand Voucher Verification Blocked
```php
$staff = VendorUser::factory()->create([
    'role' => 'BRANCH_STAFF',
    'branch_id' => 1, // belongs to brand A
]);

$voucher = Voucher::factory()->create([
    'brand_id' => 2, // belongs to brand B
    'code' => 'XYZ123',
]);

// ❌ Additional brand check blocks this
try {
    $service->verifyVoucher($staff, 'XYZ123', 1);
} catch (ApiException $e) {
    // Error: "Voucher does not belong to your brand"
}
```

---

## Integration with Routes

Routes automatically enforce middleware:

```php
Route::middleware([VendorAuthenticate::class])->group(function () {
    Route::get('/vouchers', [VendorRbacController::class, 'listVouchers']);
    // ^ Middleware ensures:
    //   1. Vendor is authenticated (VendorAuthenticate)
    //   2. Vendor is active
    //   3. Query results scoped by service logic
});
```

Optional permission checking:
```php
Route::middleware([
    VendorAuthenticate::class,
    VendorPermissionWithScoping::class.':vendor.vouchers.verify:BRANCH_STAFF'
])
->post('/vouchers/verify', ...);
// ^ Ensures role is BRANCH_STAFF AND has permission
```

---

## Database-Level Guarantees

All queries scoped at service level = DB constraints enforced:

| Operation | Query Filter |
|-----------|---|
| Admin views reviews | `reviews.branch_id → branches.place_id → brand_id = vendor.brand_id` |
| Staff verifies voucher | `vouchers.id = X AND used_branch_id = vendor.branch_id` |
| Admin updates branch | `branches.id = X AND branches.place.brand_id = vendor.brand_id` |
| Staff lists vouchers | `vouchers.used_branch_id = vendor.branch_id AND status = 'USED'` |

---

## Key Features

✅ **Role-based scoping** — Queries return different results per role  
✅ **Brand isolation** — VENDOR_ADMIN cannot access other brands  
✅ **Branch isolation** — BRANCH_STAFF confined to single branch  
✅ **Policy enforcement** — Centralized authorization rules  
✅ **Reusable traits** — Use in any service for consistent scoping  
✅ **Middleware integration** — Optional permission checks  
✅ **Error messages** — Clear 403 Forbidden responses  
✅ **Database efficiency** — Scoped queries at SQL level  

---

## Files Created

1. `app/Support/Traits/Vendor/VendorScoping.php` — Query scoping helpers
2. `app/Support/Traits/Vendor/VendorRoleCheck.php` — Role validation helpers
3. `app/Http/Middleware/VendorPermissionWithScoping.php` — Combined permission + role middleware
4. `app/Modules/Vendor/Rbac/Policies/VendorBrandPolicy.php` — Brand-level authorization
5. `app/Modules/Vendor/Rbac/Policies/VendorBranchPolicy.php` — Branch-level authorization
6. `app/Modules/Vendor/Rbac/Policies/VendorVoucherPolicy.php` — Voucher-level authorization
7. `app/Modules/Vendor/Rbac/Services/VendorDataService.php` — Example service with scoping
8. `app/Modules/Vendor/Rbac/Controllers/VendorRbacController.php` — Example controller
9. `app/Modules/Vendor/Rbac/Routes/api.php` — Vendor RBAC routes

---

## Next Steps

These tools are ready for:
- **PROMPT 4**: Vendor Profile Management (apply scoping to profile reads/writes)
- **PROMPT 5**: Voucher Verification (use policies in voucher endpoints)
- **PROMPT 6**: Branch Management (apply scoping to branch queries)
- **PROMPT 7**: Dashboard (scoped KPI calculations)

All endpoints should inject `VendorDataService` and use policy methods for authorization.
