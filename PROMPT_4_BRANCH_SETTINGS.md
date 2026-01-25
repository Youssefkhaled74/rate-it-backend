# PROMPT 4: Vendor Branch Settings Implementation
**Status:** ✅ Complete  
**Date:** Current Session  
**Focus:** Review cooldown management for vendor branches

---

## Overview
Implemented vendor endpoints for managing branch settings, specifically the review cooldown period that controls how frequently the same user can submit reviews for a location.

**Key Features:**
- List all accessible branches (role-based scoping)
- View branch details with cooldown configuration
- Update review cooldown (VENDOR_ADMIN only)
- Brand and branch-level authorization

---

## Endpoints Implemented

### 1. List Branches
```
GET /api/v1/vendor/branches
```

**Access Control:**
- `VENDOR_ADMIN` → Lists all branches under their brand
- `BRANCH_STAFF` → Lists only their assigned branch

**Response:** 
```json
{
  "success": true,
  "message": "vendor.branches.list",
  "data": [
    {
      "id": 1,
      "place_id": 5,
      "name": "Downtown Location",
      "address": "123 Main St",
      "lat": "40.7128",
      "lng": "-74.0060",
      "qr_code_value": "BRANCH_001",
      "qr_generated_at": "2024-01-15T10:00:00Z",
      "review_cooldown_days": 7,
      "working_hours": {"mon": "09:00-18:00"},
      "place": {...},
      "created_at": "2024-01-15T10:00:00Z",
      "updated_at": "2024-01-15T10:00:00Z"
    }
  ]
}
```

### 2. View Branch Details
```
GET /api/v1/vendor/branches/{branchId}
```

**Authorization:** VendorBranchPolicy::view (both VENDOR_ADMIN and BRANCH_STAFF allowed)

**Response:** Single branch object with all fields (same schema as list)

**Error Cases:**
- `403 Forbidden` - Cross-brand access attempted
- `404 Not Found` - Branch doesn't exist or unauthorized

### 3. Update Review Cooldown
```
PATCH /api/v1/vendor/branches/{branchId}/cooldown
```

**Access Control:** `VENDOR_ADMIN` only (via VendorBranchPolicy::manage)

**Request Body:**
```json
{
  "review_cooldown_days": 14
}
```

**Validation:**
- `review_cooldown_days`: required, integer, minimum 0, maximum 365

**Response:** Updated branch object with new cooldown value

**Error Cases:**
- `422 Unprocessable Entity` - Invalid cooldown value
- `403 Forbidden` - User is BRANCH_STAFF (doesn't have manage permission)
- `403 Forbidden` - Cross-brand access attempted
- `404 Not Found` - Branch doesn't exist

---

## Files Created

### 1. Request Validation
**File:** [app/Modules/Vendor/Branches/Requests/UpdateBranchCooldownRequest.php](app/Modules/Vendor/Branches/Requests/UpdateBranchCooldownRequest.php)

Validates incoming cooldown update request:
- `review_cooldown_days`: required, integer, between 0-365
- Uses i18n keys for validation messages
- Authorization built-in via middleware/policy

### 2. Resource Serializer
**File:** [app/Modules/Vendor/Branches/Resources/BranchDetailsResource.php](app/Modules/Vendor/Branches/Resources/BranchDetailsResource.php)

Transforms Branch model to API response:
- Includes: id, place_id, name, address, lat, lng, qr_code, qr_generated_at
- **Key Field:** `review_cooldown_days` (the feature being managed)
- Nested: `place` object with location details
- Timestamps: created_at, updated_at

### 3. Business Logic Layer
**File:** [app/Modules/Vendor/Branches/Services/BranchService.php](app/Modules/Vendor/Branches/Services/BranchService.php)

Core business logic with 4 methods:

**listBranches(VendorUser $vendor)**
- Filters by vendor's brand_id
- If BRANCH_STAFF: limits to single assigned branch
- Includes place relationship for location details
- Orders by branch name

**getBranch(VendorUser $vendor, int $branchId)**
- Enforces VendorBranchPolicy::view authorization
- Returns single branch with place relationship
- Throws ApiException on authorization failure

**updateCooldown(VendorUser $vendor, int $branchId, int $cooldownDays)**
- Enforces VendorBranchPolicy::manage (VENDOR_ADMIN only)
- Verifies vendor can access the branch's brand
- Updates review_cooldown_days field
- Returns updated branch for response

**find(int $id)** (private)
- Internal helper for branch lookup
- Used by public methods

**Key Design Decisions:**
- Uses VendorScoping trait methods: `getVendorBrandId()`, `isBranchStaff()`, `vendorCanAccessBrand()`
- Authorization via both middleware and service layer (defense in depth)
- Query filtering prevents cross-brand/cross-branch data access
- Exceptions handled with proper i18n keys

### 4. HTTP Controller
**File:** [app/Modules/Vendor/Branches/Controllers/BranchController.php](app/Modules/Vendor/Branches/Controllers/BranchController.php)

Maps HTTP requests to business logic:

**index()**
- Route: GET /api/v1/vendor/branches
- Calls: service.listBranches()
- Returns: collection via BranchDetailsResource
- Error: Returns 403 on policy violation

**show(string $branchId)**
- Route: GET /api/v1/vendor/branches/{branchId}
- Calls: service.getBranch()
- Returns: single resource
- Error: Handles ApiException status codes

**updateCooldown(string $branchId, UpdateBranchCooldownRequest $request)**
- Route: PATCH /api/v1/vendor/branches/{branchId}/cooldown
- Validates: UpdateBranchCooldownRequest
- Calls: service.updateCooldown()
- Returns: 200 with updated resource

### 5. Route Registration
**File:** [app/Modules/Vendor/Branches/Routes/api.php](app/Modules/Vendor/Branches/Routes/api.php)

Defines all three endpoints:
```php
Route::middleware([VendorAuthenticate::class])
    ->prefix('branches')
    ->group(function () {
        Route::get('/', [BranchController::class, 'index']);
        Route::get('{branchId}', [BranchController::class, 'show']);
        Route::patch('{branchId}/cooldown', [BranchController::class, 'updateCooldown']);
    });
```

### 6. Language Files (Updated)
**Files:** 
- [resources/lang/en/auth.php](resources/lang/en/auth.php) (added 'forbidden' key)
- [resources/lang/ar/auth.php](resources/lang/ar/auth.php) (added 'forbidden' key)
- [resources/lang/en/vendor.php](resources/lang/en/vendor.php) (NEW - branch messages)
- [resources/lang/ar/vendor.php](resources/lang/ar/vendor.php) (NEW - branch messages Arabic)

**Keys Added:**
- `auth.forbidden` - "Forbidden" / "ممنوع"
- `vendor.branches.list` - "Branches list" / "قائمة الفروع"
- `vendor.branches.details` - "Branch details" / "تفاصيل الفرع"
- `vendor.branches.cooldown.updated` - "Review cooldown updated successfully" / "تم تحديث فترة الانتظار بنجاح"

### 7. Main Routes Registration (Updated)
**File:** [routes/api.php](routes/api.php)

Added require statement to include Vendor\Branches routes:
```php
// Vendor branches management
require base_path('app/Modules/Vendor/Branches/Routes/api.php');
```

Placed after Vendor authentication routes, ensuring VendorAuthenticate middleware is available.

---

## Authorization Flow

### Middleware Layer
1. `VendorAuthenticate` - Validates Sanctum token and attaches vendor to request
2. Implicit in routing - Routes only accessible to authenticated vendors

### Service Layer
Each BranchService method includes explicit authorization:

```php
// For view operations
VendorBranchPolicy::authorize($vendor, 'view', $branchId);

// For manage operations  
VendorBranchPolicy::authorize($vendor, 'manage', $branchId);
```

### Policy Rules
- **view**: Allowed if:
  - Branch's brand = vendor's brand
  - Both VENDOR_ADMIN and BRANCH_STAFF allowed

- **manage**: Allowed if:
  - Branch's brand = vendor's brand
  - VENDOR_ADMIN role only (BRANCH_STAFF denied)

### Query Filtering
VendorScoping trait ensures database-level filtering:
```php
// VENDOR_ADMIN query
SELECT * FROM branches 
WHERE place.brand_id = vendor.brand_id

// BRANCH_STAFF query  
SELECT * FROM branches
WHERE branches.id = vendor.branch_id
```

---

## Integration Points

### 1. Database Schema
Assumes `branches` table with:
- `id` (primary key)
- `place_id` (foreign key)
- `review_cooldown_days` (integer, default 0)
- Standard timestamps
- Verified to exist via Branch model inspection

### 2. Existing Models Used
- **Branch** - Database model with relationships to Place
- **Place** - Location model with brand_id relationship
- **VendorUser** - Authenticated user with brand_id and branch_id

### 3. Traits & Policies
- **VendorScoping** - Query filtering methods
- **VendorBranchPolicy** - Authorization decisions
- **VendorAuthenticate** - Middleware

---

## Testing Scenarios

### Test Case 1: VENDOR_ADMIN Lists Branches
```
Authorization: Token {admin_token}
GET /api/v1/vendor/branches
Expected: 200 with all branches under admin's brand
```

### Test Case 2: BRANCH_STAFF Lists Branches  
```
Authorization: Token {staff_token}
GET /api/v1/vendor/branches
Expected: 200 with only their assigned branch
```

### Test Case 3: Cross-Brand Access Attempt
```
Authorization: Token {vendor_from_brand_1}
GET /api/v1/vendor/branches/999  # Branch from brand 2
Expected: 403 Forbidden
```

### Test Case 4: BRANCH_STAFF Tries to Update Cooldown
```
Authorization: Token {staff_token}
PATCH /api/v1/vendor/branches/123/cooldown
Body: {"review_cooldown_days": 14}
Expected: 403 Forbidden (policy denies manage for non-admin)
```

### Test Case 5: VENDOR_ADMIN Updates Cooldown
```
Authorization: Token {admin_token}
PATCH /api/v1/vendor/branches/123/cooldown
Body: {"review_cooldown_days": 14}
Expected: 200 with updated branch including new cooldown_days value
```

### Test Case 6: Invalid Cooldown Value
```
Authorization: Token {admin_token}
PATCH /api/v1/vendor/branches/123/cooldown
Body: {"review_cooldown_days": 500}  # Exceeds max 365
Expected: 422 Unprocessable Entity with validation error
```

---

## Error Handling

**Authorization Errors (403):**
- Thrown by `VendorBranchPolicy::authorize()` when:
  - User is BRANCH_STAFF attempting 'manage' operation
  - Branch belongs to different brand
  - User has no access to branch

**Validation Errors (422):**
- Invalid cooldown value (not integer, out of range)
- Missing required fields
- Type mismatches

**Not Found Errors (404):**
- Branch ID doesn't exist
- Authorization also validates existence

**Response Format:**
```json
{
  "success": false,
  "message": "auth.forbidden",
  "data": null,
  "meta": null
}
```

---

## Dependencies & Requirements

**Framework:** Laravel 12  
**Authentication:** Sanctum multi-guard (vendor guard)  
**Models:** Branch, Place, VendorUser  
**Traits:** VendorScoping, VendorRoleCheck  
**Policies:** VendorBranchPolicy  
**Middleware:** VendorAuthenticate  

**No Database Migrations Required** - `review_cooldown_days` field already exists in branches table (default: 0)

---

## Architecture Notes

### Separation of Concerns
- **Request** - Input validation only
- **Service** - Business logic + authorization
- **Controller** - HTTP mapping + error handling
- **Resource** - Output serialization
- **Routes** - Endpoint registration

### Authorization Defense-in-Depth
- Middleware: VendorAuthenticate (validates token)
- Implicit: Routes within group (requires auth)
- Explicit: Service methods (policy checks)
- Database: VendorScoping (query filtering)

### Reusability
- BranchService can be used by other features (dashboard, reports)
- BranchDetailsResource used for all branch endpoints
- Policies apply to future operations (delete, archive, etc.)

---

## Next Steps (PROMPT 5)

Following module completion:
1. **Voucher Verification** - Endpoints for BRANCH_STAFF to verify vouchers at their location
2. **Vendor Dashboard** - KPI summary for VENDOR_ADMIN (reviews, vouchers, ratings)
3. **Vendor Notifications** - Notification retrieval and marking read
4. **RBAC Seeding** - Database seeder for vendor roles and permissions
5. **Testing & Polish** - Final validation and API documentation

---

**Summary:** Vendor branch settings management fully implemented with proper authorization, query scoping, and i18n support. Ready for integration testing and progression to voucher verification features.
