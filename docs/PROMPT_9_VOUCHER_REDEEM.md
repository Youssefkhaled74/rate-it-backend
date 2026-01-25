# PROMPT 9: Voucher Redemption with Atomic Transactions & Row Locking

**Status:** ✅ COMPLETE  
**Session:** PROMPT 9 Implementation  
**Date:** 2024

---

## Overview

Implemented **POST /api/v1/vendor/vouchers/redeem** endpoint for safe, concurrent voucher redemption with atomic database transactions and pessimistic row locking (SELECT ... FOR UPDATE).

**Key Features:**
- ✅ Atomic transaction (all-or-nothing updates)
- ✅ Pessimistic row locking (prevents race conditions)
- ✅ Status validation (only VALID vouchers redeem)
- ✅ Expiry checking (mark EXPIRED if past expiry date)
- ✅ Role-based branch enforcement (BRANCH_STAFF forced, VENDOR_ADMIN required)
- ✅ Brand scoping (only redeem vouchers from vendor's brand)
- ✅ Tracked redemption (timestamp + verified_by vendor)

---

## Architecture

### Request Flow
```
RedeemVoucherRequest (validation)
    ↓
VouchersController.redeem()
    ↓
VoucherRedeemService.redeem() [Atomic Transaction + Row Lock]
    ├─ Normalize code (URL/prefix parsing)
    ├─ Lock voucher row (SELECT ... FOR UPDATE)
    ├─ Validate status (not USED, not EXPIRED)
    ├─ Check expiry (if expires_at < now)
    ├─ Verify branch belongs to vendor's brand
    ├─ Update status → USED, set used_at, used_branch_id, verified_by_vendor_user_id
    └─ Load relationships
    ↓
VoucherRedeemResource (response serialization)
```

### Database-Level Safety

**Transaction Pattern:**
```php
DB::transaction(function() {
    // CRITICAL: Lock prevents concurrent updates
    $voucher = Voucher::where('code', $code)
        ->lockForUpdate()  // SELECT ... FOR UPDATE
        ->first();
    
    // Validate + update atomically within transaction
    // If exception thrown, entire transaction rolls back
    $voucher->status = 'USED';
    $voucher->save();
    
    return $voucher;
});
```

**How It Works:**
1. **Lock Acquisition:** `lockForUpdate()` issues `SELECT ... FOR UPDATE` to lock the row
   - MySQL: Row-level lock, prevents concurrent updates
   - SQLite: Transaction-level lock (limited but safe)
   - PostgreSQL: Compatible
2. **Atomic Update:** All changes within closure execute atomically
3. **Automatic Rollback:** Exception → transaction rolled back, lock released
4. **Prevents Race Conditions:** Second request blocks on lock until first completes

**Scenario Prevention:**
```
T1: Check status=VALID ✓
    T2: Check status=VALID ✓  [RACE CONDITION - Without locking, both see VALID]
T1: Update to USED
T2: Update to USED  [BOTH REDEEM - Same voucher redeemed twice!]

With SELECT ... FOR UPDATE:
T1: Lock row, check status=VALID ✓, update to USED
T2: Wait for lock... [Blocks]
T2: Lock acquired, check status=USED ✗, reject
```

---

## API Endpoint

### POST /api/v1/vendor/vouchers/redeem

**Authentication:** Required (Vendor Token)  
**Roles:** VENDOR_ADMIN, BRANCH_STAFF  
**Rate Limit:** Per vendor

#### Request

```json
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 5
}
```

**Parameters:**
| Field | Type | Required | Rules | Notes |
|-------|------|----------|-------|-------|
| `code_or_link` | string | Yes | max:500 | Plain code, prefixed (VOUCHER-XXX), or URL with ?code= or ?voucher= param |
| `branch_id` | integer | Conditional | exists:branches | VENDOR_ADMIN: Required. BRANCH_STAFF: Ignored (forced to their branch) |

#### Code Format Support

The endpoint normalizes all code formats:

```
Plain Code:        "ABC123"
Prefixed Code:     "VOUCHER-ABC123"
URL with code:     "https://example.com?code=ABC123"
URL with voucher:  "https://example.com?voucher=ABC123"
```

**Normalization Logic:**
1. Trim whitespace
2. Detect URL via `filter_var(FILTER_VALIDATE_URL)`
3. If URL → extract code from query params (code or voucher)
4. If contains hyphen → extract after last hyphen
5. Uppercase final code

#### Response - Success (200 OK)

```json
{
  "success": true,
  "message": "vendor.vouchers.redeemed",
  "data": {
    "id": 42,
    "code": "ABC123",
    "status": "USED",
    "issued_at": {
      "date": "2024-01-15T10:30:00Z",
      "timestamp": 1705316400
    },
    "expires_at": {
      "date": "2024-12-31T23:59:59Z",
      "timestamp": 1735689599
    },
    "used_at": {
      "date": "2024-09-20T14:22:15Z",
      "timestamp": 1726769735
    },
    "used_branch": {
      "id": 5,
      "name": "Downtown Branch"
    },
    "verified_by": {
      "id": 12,
      "name": "Ahmed Hassan",
      "phone": "+201001234567"
    },
    "brand": {
      "id": 3,
      "name": "Pizza Palace"
    }
  },
  "meta": null
}
```

#### Response - Errors

| Status | Message Key | Scenario |
|--------|-------------|----------|
| 404 | `vendor.vouchers.not_found` | Code doesn't exist |
| 403 | `auth.forbidden` | Voucher not in vendor's brand OR branch not in vendor's brand |
| 422 | `vendor.vouchers.invalid_code` | Code format unparseable |
| 422 | `vendor.vouchers.branch_id_required` | VENDOR_ADMIN didn't provide branch_id |
| 422 | `vendor.vouchers.already_redeemed` | Voucher status already USED |
| 422 | `vendor.vouchers.already_expired` | Voucher status already EXPIRED |
| 422 | `vendor.vouchers.voucher_expired` | Voucher past expiry_at date |

**Example Error (422):**
```json
{
  "success": false,
  "message": "vendor.vouchers.already_redeemed",
  "data": null,
  "meta": null
}
```

---

## Role-Based Behavior

### BRANCH_STAFF
- Can redeem vouchers at their assigned branch only
- `branch_id` in request: Ignored (forced to their branch)
- Authorization: Middleware VendorAuthenticate validates token

**Example:**
```json
// Request (branch_id is ignored)
POST /api/v1/vendor/vouchers/redeem
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 999  // Ignored!
}

// Service forces to their branch:
$branchId = $vendor->branch_id;  // e.g., 5
```

### VENDOR_ADMIN
- Can redeem vouchers at any branch in their brand
- `branch_id` in request: Required
- Validation: Branch must exist and belong to their brand

**Example:**
```json
// Request (branch_id required)
POST /api/v1/vendor/vouchers/redeem
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 5  // Must be specified
}

// Service validates:
$branch = Branch::with('place')->find(5);
assert($branch->place->brand_id === $vendor->brand_id);  // Must match
```

---

## Implementation Details

### Files Created

1. **VoucherRedeemService** (`app/Modules/Vendor/Vouchers/Services/VoucherRedeemService.php`)
   - Method: `redeem(VendorUser, string, ?int): Voucher`
   - Uses: DB::transaction, lockForUpdate, VendorScoping, VendorRoleCheck traits
   - Code normalization logic (URL parsing, prefix handling)

2. **RedeemVoucherRequest** (`app/Modules/Vendor/Vouchers/Requests/RedeemVoucherRequest.php`)
   - Rules: code_or_link (required, max 500), branch_id (nullable, exists)

3. **VoucherRedeemResource** (`app/Modules/Vendor/Vouchers/Resources/VoucherRedeemResource.php`)
   - Serializes: id, code, status, issued_at, expires_at, used_at, used_branch, verified_by, brand
   - Uses: TimestampResource for datetime formatting

### Files Updated

1. **VouchersController** (`app/Modules/Vendor/Vouchers/Controllers/VouchersController.php`)
   - Added: `redeem(RedeemVoucherRequest): ApiResponse`
   - Constructor updated to inject VoucherRedeemService
   - Error handling with ApiException try/catch

2. **Routes/api.php** (`app/Modules/Vendor/Vouchers/Routes/api.php`)
   - Added: `POST /api/v1/vendor/vouchers/redeem` route

3. **Language Files**
   - EN (`resources/lang/en/vendor.php`): 5 new keys
   - AR (`resources/lang/ar/vendor.php`): 5 new keys

---

## Database Changes

### Voucher Schema (Pre-existing)
```sql
CREATE TABLE vouchers (
    id BIGINT PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('VALID', 'USED', 'EXPIRED') DEFAULT 'VALID',
    issued_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP,
    used_at TIMESTAMP,
    used_branch_id BIGINT,
    verified_by_vendor_user_id BIGINT,
    brand_id BIGINT NOT NULL,
    
    FOREIGN KEY (used_branch_id) REFERENCES branches(id),
    FOREIGN KEY (verified_by_vendor_user_id) REFERENCES vendor_users(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);
```

### Relationships (Voucher Model)
```php
public function usedBranch() { return $this->belongsTo(Branch::class, 'used_branch_id'); }
public function verifiedByVendor() { return $this->belongsTo(VendorUser::class, 'verified_by_vendor_user_id'); }
public function brand() { return $this->belongsTo(Brand::class); }
```

---

## Transaction Safety Guarantees

### Atomicity (ACID-A)
- ✅ All-or-nothing updates: Lock → validate → update → release
- ✅ Concurrent requests serialize on row lock
- ✅ Exception rolls back entire transaction

### Consistency (ACID-C)
- ✅ No partial updates (status set atomically with timestamps)
- ✅ Brand scoping enforced at service layer
- ✅ Branch validation before update

### Isolation (ACID-I)
- ✅ Row lock prevents dirty reads
- ✅ SELECT FOR UPDATE serializes concurrent requests
- ✅ Each transaction sees consistent state

### Durability (ACID-D)
- ✅ MySQL enforces ACID
- ✅ Timestamp captured at commit time
- ✅ Foreign keys prevent orphaned records

---

## Testing Scenarios

### Scenario 1: Successful Redemption
```bash
POST /api/v1/vendor/vouchers/redeem
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 5
}

Response: 200 OK, status='USED', used_at=now(), used_branch_id=5
```

### Scenario 2: Code Variants
```bash
# All parse to same code ABC123
"ABC123"
"VOUCHER-ABC123"
"https://site.com?code=ABC123"
"https://site.com?voucher=ABC123"
```

### Scenario 3: Race Condition Prevention
```
Concurrency with single voucher:
Request A: 14:22:10.000 Lock acquired, validates VALID, updates
Request B: 14:22:10.001 Waits for lock...
Request A: 14:22:10.100 Lock released (status=USED)
Request B: 14:22:10.101 Lock acquired, validates USED, rejects with 422
```

### Scenario 4: Branch Staff Limitation
```bash
BRANCH_STAFF (branch_id=5) tries to redeem at branch 10:
POST /api/v1/vendor/vouchers/redeem
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 10  # Ignored, forced to 5
}

# Actually redeems at branch 5
Response: 200 OK, used_branch_id=5
```

### Scenario 5: Expiry Handling
```bash
Voucher: expires_at = '2024-01-01 00:00:00', status='VALID'
Current time: '2024-09-20 14:22:00'

POST /api/v1/vendor/vouchers/redeem
{
  "code_or_link": "VOUCHER-ABC123",
  "branch_id": 5
}

Service: Detects expires_at < now()
  → Sets status='EXPIRED'
  → Returns 422 'vendor.vouchers.voucher_expired'
```

---

## Error Handling

### ApiException Pattern
All errors thrown as `ApiException(messageKey, statusCode)`:
```php
throw new ApiException(__('vendor.vouchers.not_found'), 404);
throw new ApiException(__('vendor.vouchers.invalid_code'), 422);
throw new ApiException(__('vendor.vouchers.already_redeemed'), 422);
throw new ApiException(__('auth.forbidden'), 403);
```

Controller catches via try/catch:
```php
try {
    $voucher = $this->redeemService->redeem(...);
    return $this->success(new VoucherRedeemResource($voucher), 'vendor.vouchers.redeemed');
} catch (ApiException $e) {
    return $this->error($e->message, null, $e->statusCode);
}
```

---

## Performance Considerations

### Row Locking Impact
- **Lock Duration:** Minimal (microseconds for validation + update)
- **Blocking:** Only when two requests hit same voucher simultaneously
- **Scalability:** Acceptable for typical redemption rates

### Query Optimization
- `lockForUpdate()` on indexed `vouchers.code` column (UNIQUE index)
- Branch validation via indexed `branches.id` + `places.brand_id`
- Single query per transaction (not N+1)

### Database Support
| Database | Support | Notes |
|----------|---------|-------|
| MySQL | ✅ Full | SELECT ... FOR UPDATE native |
| PostgreSQL | ✅ Full | SELECT ... FOR UPDATE native |
| SQLite | ⚠️ Limited | Transaction-level lock (not row-level) |

---

## Voucher Workflow States

```
VALID (issued, not redeemed)
  ↓
[Check/Redeem Request]
  ├─ expires_at < now() → EXPIRED [endpoint returns 422]
  ├─ status == USED → Already redeemed [endpoint returns 422]
  └─ Valid & in-date → USED [endpoint returns 200]
```

**State Transitions:**
- VALID → USED: Only via successful redeem endpoint
- VALID → EXPIRED: Set by redeem endpoint if expiry_at exceeded
- USED → (no transitions): Once redeemed, immutable
- EXPIRED → (no transitions): Once expired, immutable

---

## Integration with Previous Prompts

### PROMPT 8 (Voucher Check)
- `check()` endpoint validates voucher exists + returns status
- `redeem()` endpoint builds on code normalization from check
- Both use same VendorUser authorization

### PROMPT 7 (Staff Management)
- VendorUser model used for verified_by tracking
- Branch Staff can redeem (forced to their branch)
- Audit trail via verified_by_vendor_user_id

### PROMPT 4 (Branch Settings)
- Branch must exist and belong to vendor's brand
- Branch info returned in redemption response
- Role-based branch enforcement (BRANCH_STAFF vs VENDOR_ADMIN)

### PROMPT 3 (RBAC & Scoping)
- VendorScoping trait for brand filtering
- VendorRoleCheck trait for role validation
- Middleware VendorAuthenticate for token validation

### PROMPT 2 (Auth)
- Sanctum vendor token required
- Guard: 'vendor' configured in auth.php
- Token issued at login, validated here

---

## Next Steps (PROMPT 9b onwards)

### Voucher Verification (BRANCH_STAFF only)
- Similar to redeem but marks as VERIFIED instead of USED
- Alternative endpoint: POST /api/v1/vendor/vouchers/verify
- Only BRANCH_STAFF can verify (admin can redeem)

### Voucher Management (Admin only)
- GET /api/v1/vendor/vouchers (list redeemed vouchers at branches)
- GET /api/v1/vendor/vouchers/{id} (detail view with redemption info)

### Dashboard Integration
- KPI: Total redeemed vouchers
- Chart: Redemptions over time
- Filter: By branch, date range

---

## Troubleshooting

### "Vendor doesn't have role check for admin"
→ Ensure VendorRoleCheck trait is used in service  
→ Check `isVendorAdmin()` and `isBranchStaff()` methods exist

### "Query timeout on lockForUpdate"
→ Check database transaction isolation level (should be default)  
→ Verify no long-running transactions blocking lock acquisition  
→ Consider adding lock timeout: `$query->lockForUpdate(timeout=5000)`

### "BRANCH_STAFF can redeem at other branches"
→ Verify middleware VendorAuthenticate validates token  
→ Check VendorUser.branch_id is set for BRANCH_STAFF  
→ Add assertion: `$this->isBranchStaff($vendor)` in service

---

## Code Examples

### Using the Redeem Endpoint (cURL)
```bash
curl -X POST http://localhost/api/v1/vendor/vouchers/redeem \
  -H "Authorization: Bearer <vendor_token>" \
  -H "Content-Type: application/json" \
  -d '{
    "code_or_link": "VOUCHER-ABC123",
    "branch_id": 5
  }'
```

### Testing with Postman
1. Collection: `postman/vendor/Vouchers.postman_collection.json`
2. Request: `[Vendor] Redeem Voucher`
3. Pre-request script: Set bearer token from login
4. Tests: Verify status, validate_at timestamp, branch_id

### Unit Test Example
```php
public function test_redeem_voucher_atomically()
{
    $vendor = VendorUser::factory()->admin()->create();
    $voucher = Voucher::factory()->valid()->create(['brand_id' => $vendor->brand_id]);
    
    $response = $this->actingAs($vendor, 'vendor')
        ->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => $voucher->code,
            'branch_id' => $vendor->brand->branches->first()->id,
        ]);
    
    $response->assertStatus(200);
    $this->assertTrue($response['data']['status'] === 'USED');
    $this->assertNotNull($response['data']['used_at']);
}
```

---

## Summary

✅ **Atomic Redemption:** DB::transaction + SELECT ... FOR UPDATE ensures concurrent safety  
✅ **Code Normalization:** Handles plain, prefixed, and URL-based voucher codes  
✅ **Role-Based Enforcement:** BRANCH_STAFF forced to their branch; VENDOR_ADMIN chooses  
✅ **Brand Scoping:** Only redeem vouchers from vendor's assigned brand  
✅ **Audit Trail:** Redemption tracked with timestamp + verified_by vendor user  
✅ **Production Ready:** Follows Laravel conventions, ACID guarantees, error handling  

