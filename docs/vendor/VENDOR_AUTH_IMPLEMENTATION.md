# Vendor Authentication API — PROMPT 2 Implementation

**Date**: January 25, 2026  
**Status**: ✅ Complete  
**Module**: `app/Modules/Vendor/Auth/`

---

## What Was Implemented

### 1. Vendor Guard Configuration
**File**: [config/auth.php](config/auth.php)
- Added `'vendor'` guard using Sanctum driver
- Added `'vendor_users'` provider pointing to `App\Models\VendorUser`

### 2. Enhanced VendorUser Model
**File**: [app/Models/VendorUser.php](app/Models/VendorUser.php)
- ✅ Added `HasApiTokens` trait (enables Sanctum token generation)
- ✅ Changed base class to `Authenticatable` (required for Sanctum)
- ✅ Added `SoftDeletes` trait (matches schema)
- ✅ Added `verifyPassword()` method (password verification via Hash)
- ✅ Added relationships:
  - `brand()` — belongs to Brand
  - `branch()` — belongs to Branch
  - `roles()` — morphToMany relationship for RBAC
- ✅ Added proper casts and fillable fields

### 3. Vendor Authentication Middleware
**Files**:
- [app/Http/Middleware/VendorAuthenticate.php](app/Http/Middleware/VendorAuthenticate.php)
  - Checks `Auth::guard('vendor')->check()`
  - Validates `is_active` status
  - Returns standard JSON error format on failure
  - Attaches vendor to request for downstream use

- [app/Http/Middleware/VendorPermission.php](app/Http/Middleware/VendorPermission.php)
  - RBAC permission checking (mirrors AdminPermission)
  - Resolves roles from `model_has_roles` table
  - Validates permission exists in `permissions` table
  - Returns 403 Forbidden on permission denial

### 4. Authentication Service
**File**: [app/Modules/Vendor/Auth/Services/AuthService.php](app/Modules/Vendor/Auth/Services/AuthService.php)
- `login(phone, password)` — Authenticates vendor, throws `ApiException` on invalid creds
  - Checks vendor exists and has password_hash
  - Validates is_active status
  - Verifies password via `verifyPassword()`
- `createTokenForVendor(vendor)` — Generates Sanctum plain-text token
- `logout(vendor)` — Revokes all tokens for logout

### 5. Form Request Validation
**File**: [app/Modules/Vendor/Auth/Requests/LoginRequest.php](app/Modules/Vendor/Auth/Requests/LoginRequest.php)
- Validates phone format (regex: `/^[0-9+\-\s()]+$/`)
- Validates password minimum 6 characters
- Custom error messages with i18n keys

### 6. Resource/Serializer
**File**: [app/Modules/Vendor/Auth/Resources/VendorUserResource.php](app/Modules/Vendor/Auth/Resources/VendorUserResource.php)
- Serializes VendorUser to JSON response
- Includes brand/branch details (nested)
- Hides password_hash automatically
- Includes timestamps with TimestampResource formatting

### 7. Controller
**File**: [app/Modules/Vendor/Auth/Controllers/AuthController.php](app/Modules/Vendor/Auth/Controllers/AuthController.php)
- `login(LoginRequest)` — POST endpoint, returns vendor + token
- `me(Request)` — GET endpoint, returns current authenticated vendor
- `logout(Request)` — POST endpoint, revokes tokens
- All use standard response wrapper format

### 8. Routes
**File**: [app/Modules/Vendor/Auth/Routes/api.php](app/Modules/Vendor/Auth/Routes/api.php)
- `POST /api/v1/vendor/auth/login` — Public
- `GET /api/v1/vendor/auth/me` — Protected by VendorAuthenticate
- `POST /api/v1/vendor/auth/logout` — Protected by VendorAuthenticate

### 9. Main Routes Registration
**File**: [routes/api.php](routes/api.php)
- Added vendor routes group under `v1/vendor` prefix
- Includes auth routes

### 10. Translations
**Files**:
- [resources/lang/en/auth.php](resources/lang/en/auth.php)
- [resources/lang/ar/auth.php](resources/lang/ar/auth.php)

Added keys:
```
'vendor_login_success'
'vendor_logout_success'
'vendor_profile'
'vendor_invalid_credentials'
'vendor_inactive'
```

---

## API Endpoints

### 1. Login
```
POST /api/v1/vendor/auth/login
Content-Type: application/json

{
  "phone": "+1234567890",
  "password": "secure_password_123"
}

Response (200):
{
  "success": true,
  "message": "Vendor login successful",
  "data": {
    "vendor": {
      "id": 1,
      "brand_id": 5,
      "branch_id": 12,
      "name": "Ahmed Al-Khaldi",
      "phone": "+1234567890",
      "email": "ahmed@brand.com",
      "role": "VENDOR_ADMIN",
      "is_active": true,
      "brand": {
        "id": 5,
        "name": "Premium Cafe",
        "logo_url": "https://..."
      },
      "branch": {
        "id": 12,
        "name": "Downtown",
        "address": "123 Main St"
      },
      "timestamps": {
        "created_at": { "date": "2026-01-15", "time": "10:30:00", "timezone": "UTC" },
        "updated_at": { "date": "2026-01-25", "time": "14:20:00", "timezone": "UTC" }
      }
    },
    "token": "1|abcdef123456..."
  },
  "meta": null
}

Response (401 - Invalid credentials):
{
  "success": false,
  "message": "Invalid phone or password",
  "data": null,
  "meta": null
}

Response (401 - Inactive):
{
  "success": false,
  "message": "Vendor account is inactive",
  "data": null,
  "meta": null
}
```

### 2. Get Current Vendor Profile
```
GET /api/v1/vendor/auth/me
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Vendor profile",
  "data": {
    "id": 1,
    "brand_id": 5,
    "branch_id": 12,
    "name": "Ahmed Al-Khaldi",
    "phone": "+1234567890",
    "email": "ahmed@brand.com",
    "role": "VENDOR_ADMIN",
    "is_active": true,
    "brand": { ... },
    "branch": { ... },
    "timestamps": { ... }
  },
  "meta": null
}

Response (401 - Missing token):
{
  "success": false,
  "message": "Unauthenticated. Please provide valid authentication credentials.",
  "data": null,
  "meta": null
}
```

### 3. Logout
```
POST /api/v1/vendor/auth/logout
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Vendor logged out",
  "data": null,
  "meta": null
}

Response (401 - Missing token):
{
  "success": false,
  "message": "Unauthenticated. Please provide valid authentication credentials.",
  "data": null,
  "meta": null
}
```

---

## Key Design Decisions

1. **Sanctum Tokens**: Follows existing patterns (Admin & User modules)
   - Plain-text tokens returned to client
   - Tokens stored in personal_access_tokens table
   - Tokens tied to specific guard via middleware

2. **Phone-Based Login**: Consistent with User module approach
   - No email-based login initially
   - Phone field is unique in vendor_users table
   - Simple password verification (no OTP for now)

3. **is_active Check**: 
   - Enforced at both middleware and service level
   - Prevents inactive vendors from accessing API
   - Allows admin to deactivate vendors without deletion

4. **RBAC Ready**:
   - VendorUser already supports `morphToMany('Role')` relationship
   - VendorPermission middleware ready for permission checks
   - Roles table uses `guard = 'vendor'` for separation

5. **Error Handling**:
   - Uses existing `ApiException` class
   - Consistent JSON response format
   - i18n message keys for localization

---

## Next Steps (Future Prompts)

**PROMPT 3**: Vendor Profile Management
- GET /api/v1/vendor/profile/me
- PUT /api/v1/vendor/profile/update
- ProfileController, ProfileService, UpdateProfileRequest

**PROMPT 4**: Voucher Verification
- GET /api/v1/vendor/vouchers/list
- POST /api/v1/vendor/vouchers/verify
- VoucherController, VoucherService, VerifyVoucherRequest

And so on...

---

## Testing Checklist

- ✅ Models: VendorUser has correct relationships
- ✅ Config: Guard and provider registered
- ✅ Middleware: Can authenticate and check permissions
- ✅ Service: Login, token creation, logout logic
- ✅ Validation: LoginRequest validates phone/password
- ✅ Resources: VendorUserResource serializes correctly
- ✅ Routes: All endpoints registered under /api/v1/vendor/auth/
- ✅ Translations: All i18n keys added (EN & AR)

All files follow existing project conventions:
- Namespace structure: `App\Modules\Vendor\Auth\{EntityType}`
- Response format: Standard wrapper from ApiResponseTrait
- Service injection: Via constructor
- Error handling: ApiException with i18n keys
