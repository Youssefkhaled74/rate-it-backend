# PROMPT 7: Vendor Staff Management Implementation
**Status:** ✅ Complete  
**Date:** Current Session  
**Focus:** CRUD operations for managing BRANCH_STAFF users under VENDOR_ADMIN

---

## Overview
Implemented vendor endpoints for managing branch staff members. VENDOR_ADMIN users can create, list, update, and reset passwords for BRANCH_STAFF members assigned to their brand's branches.

**Key Features:**
- List staff members with filtering and pagination
- Create new BRANCH_STAFF members (role auto-fixed)
- Update staff details (name, branch assignment, active status)
- Reset staff passwords securely
- Brand-level scoping (staff must belong to admin's brand)
- Branch validation (staff branch must be under admin's brand)

---

## Endpoints Implemented

### 1. List Staff Members
```
GET /api/v1/vendor/staff
```

**Access Control:**
- `VENDOR_ADMIN` only (enforced via `VendorPermissionWithScoping:vendor.staff.manage`)

**Query Parameters:**
- `branch_id` (optional) - Filter by assigned branch
- `q` (optional) - Search by name, phone, or email
- `is_active` (optional, boolean) - Filter by active status
- `page` (optional, default 1) - Pagination page number
- `per_page` (optional, default 15, max 200) - Results per page

**Response Example:**
```json
{
  "success": true,
  "message": "vendor.staff.list",
  "data": [
    {
      "id": 12,
      "name": "Ahmed Hassan",
      "phone": "+971501234567",
      "email": "ahmed@example.com",
      "is_active": true,
      "branch": {
        "id": 5,
        "name": "Downtown Location"
      },
      "created_at": {
        "date": "2024-01-25",
        "time": "10:30:00",
        "timezone": "UTC",
        "timestamp": 1706169000
      }
    }
  ],
  "meta": {
    "pagination": {
      "total": 8,
      "count": 1,
      "per_page": 15,
      "current_page": 1,
      "last_page": 1
    }
  }
}
```

### 2. View Staff Details
```
GET /api/v1/vendor/staff/{id}
```

**Access Control:**
- `VENDOR_ADMIN` only
- Staff must belong to admin's brand

**Response Example:**
```json
{
  "success": true,
  "message": "vendor.staff.details",
  "data": {
    "id": 12,
    "name": "Ahmed Hassan",
    "phone": "+971501234567",
    "email": "ahmed@example.com",
    "is_active": true,
    "role": "BRANCH_STAFF",
    "branch": {
      "id": 5,
      "name": "Downtown Location",
      "address": "123 Main Street, Dubai"
    },
    "brand": {
      "id": 2,
      "name": "McDonald's UAE"
    },
    "created_at": {...},
    "updated_at": {...}
  }
}
```

### 3. Create Staff Member
```
POST /api/v1/vendor/staff
```

**Access Control:**
- `VENDOR_ADMIN` only
- Branch must belong to admin's brand

**Request Body:**
```json
{
  "name": "Ahmed Hassan",
  "phone": "+971501234567",
  "email": "ahmed@example.com",
  "branch_id": 5
}
```

**Validation Rules:**
- `name`: required, string, max 255
- `phone`: required, regex pattern, unique in vendor_users, max 20
- `email`: nullable, email format, unique in vendor_users
- `branch_id`: required, must exist in branches table

**Response:** Same as View Staff Details (201 Created)

**Error Cases:**
- `422 Unprocessable Entity` - Validation error
- `400 Bad Request` - Branch doesn't belong to admin's brand

### 4. Update Staff Member
```
PATCH /api/v1/vendor/staff/{id}
```

**Access Control:**
- `VENDOR_ADMIN` only
- Staff must belong to admin's brand

**Request Body:**
```json
{
  "name": "Ahmed Hassan Updated",
  "branch_id": 6,
  "is_active": true
}
```

**Validation Rules:**
- `name`: nullable, string, max 255 (only updated if provided)
- `branch_id`: nullable, must exist in branches (only updated if provided)
- `is_active`: nullable, boolean (only updated if provided)

**Response:** Updated staff object

**Error Cases:**
- `404 Not Found` - Staff doesn't exist or belongs to different brand
- `422 Unprocessable Entity` - New branch doesn't belong to admin's brand

### 5. Reset Staff Password
```
POST /api/v1/vendor/staff/{id}/reset-password
```

**Access Control:**
- `VENDOR_ADMIN` only
- Staff must belong to admin's brand

**Request Body:**
```json
{
  "new_password": "SecurePassword123",
  "new_password_confirmation": "SecurePassword123"
}
```

**Validation Rules:**
- `new_password`: required, string, minimum 6 characters, confirmed
- Password confirmation must match

**Response:** Updated staff object with new password applied

**Effect:**
- Updates password_hash field
- Revokes all existing Sanctum tokens (forces re-login)
- Returns updated staff details

**Error Cases:**
- `404 Not Found` - Staff doesn't exist
- `422 Unprocessable Entity` - Password validation failed

---

## Files Created

### 1. Request Validation
**Files:**
- [app/Modules/Vendor/Staff/Requests/ListStaffRequest.php](app/Modules/Vendor/Staff/Requests/ListStaffRequest.php)
- [app/Modules/Vendor/Staff/Requests/StoreStaffRequest.php](app/Modules/Vendor/Staff/Requests/StoreStaffRequest.php)
- [app/Modules/Vendor/Staff/Requests/UpdateStaffRequest.php](app/Modules/Vendor/Staff/Requests/UpdateStaffRequest.php)
- [app/Modules/Vendor/Staff/Requests/ResetStaffPasswordRequest.php](app/Modules/Vendor/Staff/Requests/ResetStaffPasswordRequest.php)

**Features:**
- ListStaffRequest: filters for pagination, search, branch, active status
- StoreStaffRequest: validates required fields + phone uniqueness
- UpdateStaffRequest: optional fields (partial updates)
- ResetStaffPasswordRequest: password confirmation validation

### 2. Resources
**Files:**
- [app/Modules/Vendor/Staff/Resources/StaffListResource.php](app/Modules/Vendor/Staff/Resources/StaffListResource.php)
- [app/Modules/Vendor/Staff/Resources/StaffDetailResource.php](app/Modules/Vendor/Staff/Resources/StaffDetailResource.php)

**Features:**
- StaffListResource: id, name, phone, email, is_active, branch, created_at
- StaffDetailResource: all list fields + role, branch address, brand info, updated_at
- Both hide password_hash field automatically

### 3. Business Logic Service
**File:** [app/Modules/Vendor/Staff/Services/VendorStaffService.php](app/Modules/Vendor/Staff/Services/VendorStaffService.php)

**Methods:**

**list(VendorUser $vendor, array $filters): LengthAwarePaginator**
- Queries BRANCH_STAFF role only for vendor's brand
- Filters by branch (with validation)
- Searches by name, phone, email
- Filters by active status
- Returns paginated results

**find(VendorUser $vendor, int $staffId): ?VendorUser**
- Queries single staff member
- Enforces brand-level and role scoping
- Returns null if not found

**create(VendorUser $vendor, array $data): VendorUser**
- Validates branch belongs to vendor's brand
- Creates staff with BRANCH_STAFF role fixed
- Hashes password using Laravel Hash facade
- Returns created staff with temporary_password attribute

**update(VendorUser $vendor, int $staffId, array $data): ?VendorUser**
- Only updates provided fields
- Validates branch if changing assignment
- Enforces brand scoping
- Returns null if staff not found

**resetPassword(VendorUser $vendor, int $staffId, string $newPassword): ?VendorUser**
- Hashes new password
- Revokes all existing tokens (forces re-login)
- Returns null if staff not found

**Key Design Decisions:**
- Uses VendorScoping trait for consistent brand access
- Role fixed to BRANCH_STAFF (not updatable)
- Branch validation at service level (prevents cross-brand assignment)
- Password stored hashed via Hash::make() (never plain-text)
- Token revocation on password reset for security

### 4. HTTP Controller
**File:** [app/Modules/Vendor/Staff/Controllers/StaffController.php](app/Modules/Vendor/Staff/Controllers/StaffController.php)

**Endpoints:**

**index(ListStaffRequest $request)**
- GET /api/v1/vendor/staff
- Validates filters
- Returns paginated list

**show(string $id)**
- GET /api/v1/vendor/staff/{id}
- Returns single staff details or 404

**store(StoreStaffRequest $request)**
- POST /api/v1/vendor/staff
- Validates request
- Returns 201 Created with staff details

**update(string $id, UpdateStaffRequest $request)**
- PATCH /api/v1/vendor/staff/{id}
- Validates request (all fields optional)
- Returns 200 with updated staff

**resetPassword(string $id, ResetStaffPasswordRequest $request)**
- POST /api/v1/vendor/staff/{id}/reset-password
- Validates password confirmation
- Returns 200 with updated staff

### 5. Route Registration
**File:** [app/Modules/Vendor/Staff/Routes/api.php](app/Modules/Vendor/Staff/Routes/api.php)

Routes all protected by:
- `VendorAuthenticate` - Token validation
- `VendorPermissionWithScoping:vendor.staff.manage` - VENDOR_ADMIN role enforcement

### 6. Language Files (Updated)
**Files:**
- [resources/lang/en/vendor.php](resources/lang/en/vendor.php) (updated)
- [resources/lang/ar/vendor.php](resources/lang/ar/vendor.php) (updated)

**Keys Added:**
- `vendor.staff.list` - "Staff list" / "قائمة الموظفين"
- `vendor.staff.details` - "Staff details" / "تفاصيل الموظف"
- `vendor.staff.created` - "Staff member created successfully" / "تم إنشاء الموظف بنجاح"
- `vendor.staff.updated` - "Staff member updated successfully" / "تم تحديث الموظف بنجاح"
- `vendor.staff.password_reset` - "Password reset successfully" / "تم إعادة تعيين كلمة المرور بنجاح"
- `vendor.staff.not_found` - "Staff member not found" / "الموظف غير موجود"

### 7. Main Routes (Updated)
**File:** [routes/api.php](routes/api.php)

Added vendor staff routes before RBAC routes:
```php
// Vendor staff management
require base_path('app/Modules/Vendor/Staff/Routes/api.php');
```

---

## Authorization Flow

### Middleware Layer
1. `VendorAuthenticate` - Validates token, attaches authenticated vendor
2. `VendorPermissionWithScoping:vendor.staff.manage` - Enforces VENDOR_ADMIN role

### Service Layer
All methods enforce brand-level scoping:
- List: `where('brand_id', $brandId)`
- Find: `where('brand_id', $brandId)`
- Create/Update: Validates branch belongs to vendor's brand
- ResetPassword: Brand scoping via find()

### Data Validation
- Phone: regex pattern `/^[0-9+\-\s()]+$/`
- Phone: unique constraint in database
- Email: unique constraint in database
- Branch: must exist and belong to vendor's brand

---

## Security Considerations

### Password Handling
- Passwords stored as bcrypt hash (never plain-text)
- Hash::check() used for verification during login
- New passwords confirmed via "confirmed" rule
- All tokens revoked on password reset

### Brand Isolation
- Staff must belong to admin's brand_id
- Branches must belong to admin's brand (via place relationship)
- All queries scoped to brand_id to prevent cross-brand access

### Role Enforcement
- Staff role fixed to BRANCH_STAFF (not updatable)
- Admin role required for all management operations
- Enforced at middleware level + service level

### Phone Uniqueness
- Unique constraint in database
- Prevents duplicate phone numbers across all vendor_users
- Validation error if attempting duplicate

---

## Testing Scenarios

### Test Case 1: VENDOR_ADMIN Lists Staff
```
Authorization: Token {admin_token}
GET /api/v1/vendor/staff
Expected: 200 with list of all BRANCH_STAFF under admin's brand
```

### Test Case 2: Filter by Branch
```
Authorization: Token {admin_token}
GET /api/v1/vendor/staff?branch_id=5
Expected: 200 with staff assigned to branch 5 only
```

### Test Case 3: Cross-Brand Branch Access
```
Authorization: Token {vendor_from_brand_1}
GET /api/v1/vendor/staff?branch_id=999  # Branch from brand 2
Expected: 200 with empty list (returns empty paginator, not error)
```

### Test Case 4: Create Staff with Validation
```
Authorization: Token {admin_token}
POST /api/v1/vendor/staff
Body: {
  "name": "New Staff",
  "phone": "+971509876543",
  "email": "staff@example.com",
  "branch_id": 5
}
Expected: 201 with created staff details
```

### Test Case 5: Create with Duplicate Phone
```
Authorization: Token {admin_token}
POST /api/v1/vendor/staff
Body: {
  "name": "Duplicate",
  "phone": "+971501234567",  # Already exists
  "branch_id": 5
}
Expected: 422 with "phone already exists" error
```

### Test Case 6: Create with Cross-Brand Branch
```
Authorization: Token {vendor_from_brand_1}
POST /api/v1/vendor/staff
Body: {
  "name": "Staff",
  "phone": "+971509876543",
  "branch_id": 999  # Branch from brand 2
}
Expected: 422 with "Branch does not belong to your brand" error
```

### Test Case 7: Update Staff Name
```
Authorization: Token {admin_token}
PATCH /api/v1/vendor/staff/12
Body: {"name": "Updated Name"}
Expected: 200 with updated staff details
```

### Test Case 8: Update Staff Branch
```
Authorization: Token {admin_token}
PATCH /api/v1/vendor/staff/12
Body: {"branch_id": 6}
Expected: 200 with staff reassigned to branch 6
```

### Test Case 9: Deactivate Staff
```
Authorization: Token {admin_token}
PATCH /api/v1/vendor/staff/12
Body: {"is_active": false}
Expected: 200 with staff marked inactive
```

### Test Case 10: Reset Password
```
Authorization: Token {admin_token}
POST /api/v1/vendor/staff/12/reset-password
Body: {
  "new_password": "NewPassword123",
  "new_password_confirmation": "NewPassword123"
}
Expected: 200 with password updated (staff must re-login)
```

### Test Case 11: Password Mismatch
```
Authorization: Token {admin_token}
POST /api/v1/vendor/staff/12/reset-password
Body: {
  "new_password": "NewPassword123",
  "new_password_confirmation": "DifferentPassword"
}
Expected: 422 with confirmation error
```

### Test Case 12: BRANCH_STAFF Denied Access
```
Authorization: Token {staff_token}
GET /api/v1/vendor/staff
Expected: 403 Forbidden (VendorPermissionWithScoping enforces ADMIN role)
```

---

## Database Constraints

**vendor_users Table:**
- `phone`: unique index
- `email`: unique index (nullable)
- `brand_id`: foreign key, nullable, on delete cascade
- `branch_id`: foreign key, nullable, on delete cascade
- Check constraint (PostgreSQL): BRANCH_STAFF must have branch_id NOT NULL

**Staff Creation Rules:**
- All BRANCH_STAFF members must have branch_id set
- All BRANCH_STAFF members must have brand_id set (derived from branch)
- Role is always BRANCH_STAFF (not configurable)
- is_active defaults to true

---

## Integration Notes

### With Existing Auth Module
- Uses same VendorUser model
- Follows same password hashing approach
- Password reset similar to User password reset
- Token management via Sanctum

### With Branch Settings (PROMPT 4)
- Staff can only be assigned to branches under admin's brand
- Both modules use VendorScoping trait for consistency
- Branches validated via place.brand_id relationship

### With Reviews (PROMPT 5)
- Staff can view/verify reviews at their assigned branch
- VENDOR_ADMIN can see all brand reviews
- This module enables staff account management

### Future Integrations
- Dashboard KPIs can include staff metrics (total staff, by branch)
- Notifications can target specific staff members
- Activity logs can track staff actions
- Audit trail for password resets

---

## Error Handling

**Authorization Errors (403):**
- Thrown when user is not VENDOR_ADMIN
- Enforced by VendorPermissionWithScoping middleware

**Validation Errors (422):**
- Phone regex pattern mismatch
- Phone uniqueness violation
- Email uniqueness violation
- Branch doesn't exist
- Branch doesn't belong to admin's brand
- Password confirmation mismatch

**Not Found Errors (404):**
- Staff ID doesn't exist
- Staff belongs to different brand

**Response Format:**
```json
{
  "success": false,
  "message": "error_key or error_message",
  "data": null,
  "meta": null
}
```

---

## Performance Considerations

### Query Optimization
- Uses `with(['branch:id,name'])` for eager loading
- Selective column loading in relationships
- Indexed queries on brand_id and branch_id
- Pagination limits to prevent full table scans

### Scalability
- per_page max 200 to prevent large dataset requests
- Index on brand_id ensures fast filtering
- Index on branch_id ensures fast filtering
- Unique constraints on phone/email prevent duplicates

---

## API Contract Summary

| Method | Endpoint | Auth | Role | Purpose |
|--------|----------|------|------|---------|
| GET | `/api/v1/vendor/staff` | Required | ADMIN | List staff |
| GET | `/api/v1/vendor/staff/{id}` | Required | ADMIN | View staff |
| POST | `/api/v1/vendor/staff` | Required | ADMIN | Create staff |
| PATCH | `/api/v1/vendor/staff/{id}` | Required | ADMIN | Update staff |
| POST | `/api/v1/vendor/staff/{id}/reset-password` | Required | ADMIN | Reset password |

---

## Next Steps (Remaining PROMPTs)

Following staff management implementation:
1. **PROMPT 5b: Voucher Verification** - Staff verify vouchers at their branch
2. **PROMPT 6: Vendor Dashboard** - Admin KPI summary
3. **PROMPT 8: Vendor Notifications** - Notification retrieval
4. **PROMPT 9: RBAC Seeding** - Add vendor.staff.manage permission to roles
5. **PROMPT 10: Testing & Polish** - Final validation

---

**Summary:** Vendor staff management fully implemented with comprehensive CRUD operations, brand isolation, and secure password handling. Staff role is fixed to BRANCH_STAFF and all operations scoped to vendor's brand. Ready for integration testing and staff member onboarding.
