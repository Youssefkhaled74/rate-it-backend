# PROMPT 8: Voucher Check Status Implementation
**Status:** ✅ Complete  
**Date:** Current Session  
**Focus:** Checking voucher status with code normalization and brand scoping

---

## Overview
Implemented voucher check endpoint allowing vendors to verify voucher status (VALID/USED/EXPIRED) by code or link. Supports multiple input formats with intelligent code extraction.

**Key Features:**
- Check voucher status with flexible input (code, code with prefix, or URL)
- Automatic code normalization and extraction
- Return detailed status information
- Brand-level scoping (voucher must belong to vendor's brand)
- Works for both VENDOR_ADMIN and BRANCH_STAFF

---

## Endpoint Implemented

### Check Voucher Status
```
POST /api/v1/vendor/vouchers/check
```

**Access Control:**
- Any authenticated vendor (both VENDOR_ADMIN and BRANCH_STAFF)
- Voucher must belong to vendor's brand

**Request Body:**
```json
{
  "code_or_link": "ABC123"
}
```

**Input Format Support:**
- Plain code: `ABC123` → normalized to `ABC123`
- Prefixed code: `VOUCHER-ABC123` → extracts to `ABC123`
- URL with code param: `https://example.com/vouchers?code=ABC123` → extracts to `ABC123`
- URL with voucher param: `https://example.com/vouchers?voucher=ABC123` → extracts to `ABC123`

**Response Example (VALID Voucher):**
```json
{
  "success": true,
  "message": "vendor.vouchers.check_success",
  "data": {
    "code": "ABC123",
    "status": "VALID",
    "issued_at": {
      "date": "2024-01-20",
      "time": "10:00:00",
      "timezone": "UTC",
      "timestamp": 1705756800
    },
    "expires_at": {
      "date": "2024-02-20",
      "time": "10:00:00",
      "timezone": "UTC",
      "timestamp": 1708435200
    },
    "brand": {
      "id": 2,
      "name": "McDonald's UAE"
    }
  }
}
```

**Response Example (USED Voucher):**
```json
{
  "success": true,
  "message": "vendor.vouchers.check_success",
  "data": {
    "code": "ABC123",
    "status": "USED",
    "issued_at": {...},
    "expires_at": {...},
    "used_at": {
      "date": "2024-01-25",
      "time": "14:30:00",
      "timezone": "UTC",
      "timestamp": 1706185800
    },
    "used_branch": {
      "id": 5,
      "name": "Downtown Location"
    },
    "verified_by": {
      "id": 12,
      "name": "Ahmed Hassan",
      "phone": "+971501234567"
    },
    "brand": {
      "id": 2,
      "name": "McDonald's UAE"
    }
  }
}
```

**Response Example (EXPIRED Voucher):**
```json
{
  "success": true,
  "message": "vendor.vouchers.check_success",
  "data": {
    "code": "ABC123",
    "status": "EXPIRED",
    "issued_at": {...},
    "expires_at": {...},
    "brand": {...}
  }
}
```

**Error Cases:**
- `422 Unprocessable Entity` - Missing code_or_link
- `422 Unprocessable Entity` - Invalid code format
- `404 Not Found` - Voucher doesn't exist
- `403 Forbidden` - Voucher belongs to different brand

---

## Files Created

### 1. Request Validation
**File:** [app/Modules/Vendor/Vouchers/Requests/CheckVoucherRequest.php](app/Modules/Vendor/Vouchers/Requests/CheckVoucherRequest.php)

Validates incoming voucher check request:
- `code_or_link`: required, string, max 500 characters
- Supports URLs, codes with prefixes, and plain codes

### 2. Resource Serializer
**File:** [app/Modules/Vendor/Vouchers/Resources/VoucherCheckResource.php](app/Modules/Vendor/Vouchers/Resources/VoucherCheckResource.php)

Transforms Voucher model to API response:
- Basic fields: code, status, issued_at, expires_at, brand
- Conditional fields (if USED): used_at, used_branch, verified_by
- Uses TimestampResource for date formatting

### 3. Business Logic Service
**File:** [app/Modules/Vendor/Vouchers/Services/VoucherCheckService.php](app/Modules/Vendor/Vouchers/Services/VoucherCheckService.php)

Core business logic with intelligent code handling:

**check(VendorUser $vendor, string $codeOrLink): Voucher**
- Normalizes input using normalizeCode()
- Queries voucher by code
- Enforces brand-level scoping
- Returns voucher with relationships loaded

**normalizeCode(string $input): ?string**
- Handles multiple input formats
- Trims whitespace
- Detects URLs and extracts code
- Removes prefixes (VOUCHER-)
- Returns uppercase code

**extractCodeFromUrl(string $url): ?string**
- Parses URL query parameters
- Looks for 'code' or 'voucher' parameters
- Returns extracted code in uppercase

**Key Design Decisions:**
- Flexible input handling (code, URL, prefixed code)
- Uses VendorScoping trait for brand access
- Brand scoping prevents cross-vendor access
- Soft 404 on not found (returns 404, not 403)
- Throws ApiException for proper error handling

### 4. HTTP Controller
**File:** [app/Modules/Vendor/Vouchers/Controllers/VouchersController.php](app/Modules/Vendor/Vouchers/Controllers/VouchersController.php)

Maps HTTP requests to business logic:

**check(CheckVoucherRequest $request)**
- Route: POST /api/v1/vendor/vouchers/check
- Validates request via CheckVoucherRequest
- Calls: service.check() with code_or_link
- Returns: resource via VoucherCheckResource
- Catches ApiException and returns appropriate status codes

### 5. Route Registration
**File:** [app/Modules/Vendor/Vouchers/Routes/api.php](app/Modules/Vendor/Vouchers/Routes/api.php)

Defines endpoint:
```php
Route::middleware([VendorAuthenticate::class])
    ->prefix('vouchers')
    ->group(function () {
        Route::post('check', [VouchersController::class, 'check']);
    });
```

Protected by `VendorAuthenticate` middleware only (no permission check - both roles allowed).

### 6. Model Updates
**File:** [app/Models/Voucher.php](app/Models/Voucher.php) (Updated)

Added relationships:
- `user()` - User who received voucher
- `brand()` - Brand voucher belongs to
- `usedBranch()` - Branch where voucher was redeemed
- `verifiedByVendor()` - Staff member who verified it

### 7. Language Files (Updated)
**Files:**
- [resources/lang/en/vendor.php](resources/lang/en/vendor.php) (updated)
- [resources/lang/ar/vendor.php](resources/lang/ar/vendor.php) (updated)

**Keys Added:**
- `vendor.vouchers.check_success` - "Voucher checked successfully" / "تم التحقق من الكوبون بنجاح"
- `vendor.vouchers.code_required` - "Voucher code or link is required" / "رمز الكوبون أو الرابط مطلوب"
- `vendor.vouchers.invalid_code` - "Invalid voucher code format" / "صيغة رمز الكوبون غير صحيحة"
- `vendor.vouchers.not_found` - "Voucher not found" / "الكوبون غير موجود"

### 8. Main Routes (Updated)
**File:** [routes/api.php](routes/api.php)

Added vendor vouchers routes:
```php
// Vendor vouchers
require base_path('app/Modules/Vendor/Vouchers/Routes/api.php');
```

---

## Authorization Flow

### Middleware Layer
- `VendorAuthenticate` - Validates Sanctum token (both VENDOR_ADMIN and BRANCH_STAFF)

### Service Layer
- Brand scoping via whereHas: ensures vendor can only check vouchers from their brand
- Returns 403 if voucher belongs to different brand
- Returns 404 if voucher doesn't exist

### Code Normalization
Multiple input formats supported:
1. **Plain code**: `ABC123` → `ABC123`
2. **Prefixed code**: `VOUCHER-ABC123` → `ABC123`
3. **URL with code**: `?code=ABC123` → `ABC123`
4. **URL with voucher**: `?voucher=ABC123` → `ABC123`

---

## Code Normalization Logic

### Input Processing Pipeline
```
Input: "VOUCHER-abc123"
  ↓
Trim whitespace
  ↓
Check if URL? No
  ↓
Check if contains hyphen? Yes
  ↓
Extract part after hyphen: "abc123"
  ↓
Convert to uppercase: "ABC123"
  ↓
Result: "ABC123"
```

### URL Processing Example
```
Input: "https://example.com/vouchers?code=ABC123"
  ↓
Parse URL
  ↓
Extract query string: "code=ABC123"
  ↓
Parse query params: ["code" => "ABC123"]
  ↓
Get "code" value: "ABC123"
  ↓
Uppercase: "ABC123"
  ↓
Result: "ABC123"
```

---

## Voucher Status Details

### VALID Status
- Voucher exists
- Issued date has passed (or null)
- Expires date is in future (or null)
- Not yet redeemed
- No used_at, used_branch, verified_by data

### USED Status
- Voucher exists
- Status field = 'USED'
- Includes used_at timestamp
- Includes used_branch details
- Includes verified_by (staff member who verified)

### EXPIRED Status
- Voucher exists
- Status field = 'EXPIRED'
- Expires date has passed
- No used_at (unless also USED)
- No used_branch or verified_by data

---

## Testing Scenarios

### Test Case 1: Check Valid Voucher with Plain Code
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "ABC123"}
Expected: 200 with VALID status and issued_at, expires_at
```

### Test Case 2: Check Valid Voucher with Prefixed Code
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "VOUCHER-ABC123"}
Expected: 200 - code normalized to ABC123, status returned
```

### Test Case 3: Check Valid Voucher with URL
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "https://example.com/redeem?code=ABC123"}
Expected: 200 - code extracted from URL parameter
```

### Test Case 4: Check Used Voucher
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "USED_CODE"}
Expected: 200 with USED status, includes used_at, used_branch, verified_by
```

### Test Case 5: Check Expired Voucher
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "EXPIRED_CODE"}
Expected: 200 with EXPIRED status
```

### Test Case 6: Cross-Brand Voucher
```
Authorization: Token {vendor_from_brand_1}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "CODE_FROM_BRAND_2"}
Expected: 403 Forbidden
```

### Test Case 7: Non-Existent Voucher
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "INVALID_CODE"}
Expected: 404 Not Found
```

### Test Case 8: Missing Code
```
Authorization: Token {vendor_token}
POST /api/v1/vendor/vouchers/check
Body: {}
Expected: 422 - code_or_link is required
```

### Test Case 9: BRANCH_STAFF Can Check Vouchers
```
Authorization: Token {branch_staff_token}
POST /api/v1/vendor/vouchers/check
Body: {"code_or_link": "ABC123"}
Expected: 200 with voucher status (same as VENDOR_ADMIN)
```

---

## Error Handling

**Missing/Invalid Code (422):**
```json
{
  "success": false,
  "message": "vendor.vouchers.code_required or vendor.vouchers.invalid_code",
  "data": null
}
```

**Voucher Not Found (404):**
```json
{
  "success": false,
  "message": "vendor.vouchers.not_found",
  "data": null
}
```

**Cross-Brand Access (403):**
```json
{
  "success": false,
  "message": "auth.forbidden",
  "data": null
}
```

---

## Database Schema

**vouchers Table:**
- `id` - Primary key
- `code` - Unique voucher code
- `status` - ENUM: VALID, USED, EXPIRED
- `issued_at` - When voucher was issued
- `expires_at` - When voucher expires
- `used_at` - When voucher was redeemed
- `used_branch_id` - FK to branches (where redeemed)
- `verified_by_vendor_user_id` - FK to vendor_users (who verified)
- `brand_id` - FK to brands (vendor filtering)

---

## Integration Notes

### With Staff Management (PROMPT 7)
- Staff member details returned in verified_by when voucher is USED
- Staff phone and name provided for accountability

### With Branches (PROMPT 4)
- Used branch details returned when voucher status is USED
- Helps track where vouchers are being redeemed

### With Future Voucher Verification (PROMPT 5b)
- Check endpoint validates voucher exists and is VALID
- Verification endpoint will mark as USED and set verified_by
- Status tracking enables redemption workflow

### With Dashboard (PROMPT 6)
- Check counts can be aggregated for analytics
- Supports "vouchers checked" metrics

---

## Performance Considerations

### Query Optimization
- Direct code lookup (indexed field)
- Selective relationship loading via with()
- No N+1 queries
- Single database query per check

### Scalability
- Code field has unique index (fast lookup)
- Brand filtering uses indexed brand_id
- No full-text search or complex joins

---

## API Contract Summary

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | `/api/v1/vendor/vouchers/check` | Required | Check voucher status |

---

## Code Normalization Examples

| Input | Output | Method |
|-------|--------|--------|
| `ABC123` | `ABC123` | Direct use |
| `abc123` | `ABC123` | Uppercase |
| `VOUCHER-ABC123` | `ABC123` | Hyphen extraction |
| `voucher-abc123` | `ABC123` | Hyphen extraction + uppercase |
| `https://example.com?code=ABC123` | `ABC123` | URL parameter extraction |
| `https://example.com?voucher=XYZ789` | `XYZ789` | URL parameter extraction |
| `https://example.com` | null | No code parameter |
| ` ` (whitespace) | null | Invalid input |

---

## Next Steps (Remaining PROMPTs)

Following voucher check implementation:
1. **PROMPT 5b: Voucher Verification** - Staff verify and mark USED
2. **PROMPT 6: Vendor Dashboard** - Include voucher check/verify metrics
3. **PROMPT 9: Vendor Notifications** - Notify on voucher status changes
4. **PROMPT 10: RBAC Seeding** - Add voucher permissions to roles
5. **PROMPT 11: Testing & Polish** - Complete API documentation

---

**Summary:** Voucher check status endpoint fully implemented with intelligent code normalization, flexible input handling, and strict brand-level scoping. Supports plain codes, prefixed codes, and URL-based code extraction. Ready for voucher verification workflow implementation.
