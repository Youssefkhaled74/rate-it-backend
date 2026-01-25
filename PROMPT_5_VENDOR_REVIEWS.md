# PROMPT 5: Vendor Reviews Implementation
**Status:** ✅ Complete  
**Date:** Current Session  
**Focus:** Review listing and details for vendor administrators

---

## Overview
Implemented vendor endpoints for viewing reviews under their brand locations. VENDOR_ADMIN users can list and filter reviews, view full details with photos and rating criteria answers.

**Key Features:**
- List reviews with advanced filtering and pagination
- Filter by branch, date range, rating range, photos
- Keyword search across comments, user names, branch names
- Full review details with photos URLs and rating criteria answers
- Strict brand-level scoping (only reviews from their brand's locations)

---

## Endpoints Implemented

### 1. List Reviews
```
GET /api/v1/vendor/reviews
```

**Access Control:**
- `VENDOR_ADMIN` only (enforced via VendorPermissionWithScoping middleware)

**Query Parameters:**
- `branch_id` (optional) - Filter by specific branch
- `date_from` (optional) - Filter reviews from this date onwards
- `date_to` (optional) - Filter reviews up to this date
- `min_rating` (optional) - Filter by minimum overall rating (0-5)
- `max_rating` (optional) - Filter by maximum overall rating (0-5)
- `has_photos` (optional, boolean) - Filter reviews that have photos
- `keyword` (optional) - Search in comments, user names, branch names
- `page` (optional, default 1) - Pagination page number
- `per_page` (optional, default 15, max 200) - Results per page

**Response Example:**
```json
{
  "success": true,
  "message": "vendor.reviews.list",
  "data": [
    {
      "id": 42,
      "overall_rating": 4.5,
      "review_score": 4.5,
      "comment": "Great service and friendly staff! Highly recommend.",
      "photos_count": 2,
      "user": {
        "nickname": "john_d",
        "phone": "+971501234567"
      },
      "branch": {
        "id": 5,
        "name": "Downtown Location"
      },
      "place": {
        "id": 8,
        "name": "McDonald's UAE"
      },
      "created_at": {
        "date": "2024-01-25",
        "time": "14:30:00",
        "timezone": "UTC",
        "timestamp": 1706185800
      }
    },
    {
      "id": 41,
      "overall_rating": 3.0,
      "review_score": 3.0,
      "comment": "Good food, slow service",
      "photos_count": 0,
      "user": {
        "nickname": "sarah_m",
        "phone": "+971509876543"
      },
      "branch": {
        "id": 5,
        "name": "Downtown Location"
      },
      "place": {
        "id": 8,
        "name": "McDonald's UAE"
      },
      "created_at": {...}
    }
  ],
  "meta": {
    "pagination": {
      "total": 156,
      "count": 15,
      "per_page": 15,
      "current_page": 1,
      "last_page": 11,
      "from": 1,
      "to": 15
    }
  }
}
```

### 2. View Review Details
```
GET /api/v1/vendor/reviews/{id}
```

**Access Control:**
- `VENDOR_ADMIN` only
- Must belong to vendor's brand locations

**Response Example:**
```json
{
  "success": true,
  "message": "vendor.reviews.details",
  "data": {
    "id": 42,
    "overall_rating": 4.5,
    "review_score": 4.5,
    "comment": "Great service and friendly staff! Highly recommend.",
    "user": {
      "nickname": "john_d",
      "phone": "+971501234567"
    },
    "branch": {
      "id": 5,
      "name": "Downtown Location",
      "address": "123 Main Street, Dubai"
    },
    "place": {
      "id": 8,
      "name": "McDonald's UAE",
      "logo_url": "https://example.com/storage/logos/mcdonalds.png"
    },
    "photos": [
      {
        "id": 1,
        "url": "https://example.com/storage/reviews/photo1.jpg",
        "file_name": "photo1.jpg",
        "created_at": "2024-01-25T14:30:00Z",
        "created_at_human": "2 hours ago"
      },
      {
        "id": 2,
        "url": "https://example.com/storage/reviews/photo2.jpg",
        "file_name": "photo2.jpg",
        "created_at": "2024-01-25T14:30:15Z",
        "created_at_human": "2 hours ago"
      }
    ],
    "answers": [
      {
        "criteria_id": 1,
        "rating_value": 5,
        "yes_no_value": null,
        "choice": null
      },
      {
        "criteria_id": 2,
        "rating_value": 4,
        "yes_no_value": null,
        "choice": null
      }
    ],
    "created_at": {
      "date": "2024-01-25",
      "time": "14:30:00",
      "timezone": "UTC",
      "timestamp": 1706185800
    }
  }
}
```

**Error Cases:**
- `404 Not Found` - Review doesn't exist or belongs to different brand
- `403 Forbidden` - User is not VENDOR_ADMIN

---

## Files Created

### 1. Request Validation
**File:** [app/Modules/Vendor/Reviews/Requests/VendorReviewsIndexRequest.php](app/Modules/Vendor/Reviews/Requests/VendorReviewsIndexRequest.php)

Validates list endpoint query parameters:
- `branch_id`: nullable, must exist in branches table
- `date_from`, `date_to`: nullable, valid dates
- `min_rating`, `max_rating`: nullable, numeric 0-5
- `has_photos`: nullable, boolean
- `keyword`: nullable, string max 255
- `page`, `per_page`: nullable, integer with min/max bounds

### 2. List Resource
**File:** [app/Modules/Vendor/Reviews/Resources/VendorReviewListResource.php](app/Modules/Vendor/Reviews/Resources/VendorReviewListResource.php)

Transforms Review model for list endpoint:
- Review fields: id, overall_rating, review_score, comment, photos_count
- User fields: nickname (ONLY), phone (ONLY)
- Branch: id, name
- Place: id, name
- Timestamp: created_at with human-readable format

### 3. Detail Resource
**File:** [app/Modules/Vendor/Reviews/Resources/VendorReviewDetailResource.php](app/Modules/Vendor/Reviews/Resources/VendorReviewDetailResource.php)

Transforms Review model for detail endpoint:
- All list fields plus
- Branch address included
- Place logo URL
- Photos collection with URLs
- Answers collection with criteria relationships

### 4. Business Logic Service
**File:** [app/Modules/Vendor/Reviews/Services/VendorReviewService.php](app/Modules/Vendor/Reviews/Services/VendorReviewService.php)

Core business logic with 2 methods:

**list(VendorUser $vendor, array $filters): LengthAwarePaginator**
- Queries reviews filtered by vendor's brand_id (via place relationship)
- Applies all filters: branch, date range, rating range, photos, keyword
- Validates branch belongs to vendor's brand
- Uses withCount(['photos']) for photos_count
- Orders by created_at descending
- Returns paginated results

**find(VendorUser $vendor, int $reviewId)**
- Queries single review with all relationships loaded
- Enforces brand-level scoping via whereHas('place', ...)
- Loads: user, branch, place, answers with criteria, photos
- Returns null if not found or belongs to different brand

**Key Design Decisions:**
- Uses VendorScoping trait to get brand_id consistently
- Query filtering prevents cross-brand data access at database level
- Validates branch_id belongs to vendor's brand before filtering
- Photos counted via withCount for efficiency
- Both methods scoped to vendor's brand

### 5. HTTP Controller
**File:** [app/Modules/Vendor/Reviews/Controllers/ReviewsController.php](app/Modules/Vendor/Reviews/Controllers/ReviewsController.php)

Maps HTTP requests to business logic:

**index(VendorReviewsIndexRequest $request)**
- Route: GET /api/v1/vendor/reviews
- Validates request via VendorReviewsIndexRequest
- Calls: service.list() with filters
- Returns: paginated collection via VendorReviewListResource
- Message: 'vendor.reviews.list'

**show(string $id)**
- Route: GET /api/v1/vendor/reviews/{id}
- Calls: service.find() with review ID
- Returns: single resource via VendorReviewDetailResource
- Message: 'vendor.reviews.details'
- Returns 404 if not found

### 6. Route Registration
**File:** [app/Modules/Vendor/Reviews/Routes/api.php](app/Modules/Vendor/Reviews/Routes/api.php)

Defines 2 endpoints:
```php
Route::middleware([
    VendorAuthenticate::class,
    VendorPermissionWithScoping::class . ':vendor.reviews.list'
])
->prefix('reviews')
->group(function () {
    Route::get('/', [ReviewsController::class, 'index']);
    Route::get('{id}', [ReviewsController::class, 'show']);
});
```

Both endpoints protected by:
1. `VendorAuthenticate` - Validates Sanctum token
2. `VendorPermissionWithScoping:vendor.reviews.list` - Enforces VENDOR_ADMIN role

### 7. Language Files (Updated)
**Files:**
- [resources/lang/en/vendor.php](resources/lang/en/vendor.php) (updated)
- [resources/lang/ar/vendor.php](resources/lang/ar/vendor.php) (updated)

**Keys Added:**
- `vendor.reviews.list` - "Reviews list" / "قائمة التقييمات"
- `vendor.reviews.details` - "Review details" / "تفاصيل التقييم"
- `vendor.reviews.not_found` - "Review not found" / "التقييم غير موجود"

### 8. Main Routes Registration (Updated)
**File:** [routes/api.php](routes/api.php)

Added vendor reviews routes require statement:
```php
// Vendor reviews
require base_path('app/Modules/Vendor/Reviews/Routes/api.php');
```

Placed after vendor branches, before vendor RBAC routes.

---

## Authorization Flow

### Middleware Layer
1. `VendorAuthenticate` - Validates Sanctum token and attaches vendor to request
2. `VendorPermissionWithScoping:vendor.reviews.list` - Enforces VENDOR_ADMIN role

### Service Layer
Both service methods implicitly enforce brand-level scoping:
- `list()` - whereHas('place', fn($q) => $q->where('brand_id', $brandId))
- `find()` - Same whereHas constraint

### Database Level
All Review queries include:
```sql
WHERE reviews.place_id IN (
  SELECT places.id FROM places WHERE places.brand_id = ?
)
```

This ensures:
- VENDOR_ADMIN from Brand A cannot see Brand B reviews
- Even if someone bypasses middleware, database-level scoping prevents data access
- Query optimization: Uses whereHas for relationship-based filtering

---

## Data Scoping Details

### Brand-Level Isolation
All reviews queried through: `Review → Branch → Place → Brand`

```
Review (place_id)
  ↓
Place (brand_id)
  ↓
Brand (id = vendor.brand_id)
```

**Query Pattern:**
```php
Review::whereHas('place', fn($q) => $q->where('brand_id', $brandId))
```

### Branch-Level Filtering
When branch_id filter applied:
1. Validate branch exists and belongs to vendor's brand
2. Add where('branch_id', $branchId) to query

```php
$branch = Branch::with('place')->find($branchId);
if (!$branch || $branch->place->brand_id !== $brandId) {
    return Review::paginate(0); // Empty result
}
$query->where('branch_id', $branchId);
```

### Photos Count Efficiency
Uses Eloquent's withCount() to avoid N+1:
```php
->withCount(['photos'])
// Result: $review->photos_count (integer, not loaded photos)
```

---

## Filter Implementation Details

### Date Range Filtering
```php
if (! empty($filters['date_from'])) {
    $query->whereDate('created_at', '>=', $filters['date_from']);
}
if (! empty($filters['date_to'])) {
    $query->whereDate('created_at', '<=', $filters['date_to']);
}
```

Uses `whereDate()` to ensure date-only comparison (ignores time portion).

### Rating Range Filtering
```php
if (isset($filters['min_rating'])) {
    $query->where('overall_rating', '>=', (float) $filters['min_rating']);
}
if (isset($filters['max_rating'])) {
    $query->where('overall_rating', '<=', (float) $filters['max_rating']);
}
```

Supports partial ratings (1.5, 3.2, etc.).

### Photos Filter
```php
if (isset($filters['has_photos']) && $filters['has_photos']) {
    $query->has('photos');
}
```

Only applied if true; false value is ignored (to show all reviews).

### Keyword Search
```php
if (! empty($filters['keyword'])) {
    $keyword = $filters['keyword'];
    $query->where(function ($q) use ($keyword) {
        $q->where('comment', 'like', "%{$keyword}%")
            ->orWhereHas('user', fn($s) => 
                $s->where('full_name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
            )
            ->orWhereHas('branch', fn($s) => 
                $s->where('name', 'like', "%{$keyword}%")
            );
    });
}
```

Searches:
- Review comment text
- User full name (not phone in original schema)
- User phone
- Branch name

---

## Testing Scenarios

### Test Case 1: VENDOR_ADMIN Lists Brand Reviews
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?per_page=10
Expected: 200 with paginated list of all reviews from admin's brand
Schema: [id, overall_rating, review_score, comment, photos_count, user, branch, place, created_at]
```

### Test Case 2: Filter by Branch
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?branch_id=5&per_page=10
Expected: 200 with only reviews from branch 5 (if it belongs to admin's brand)
```

### Test Case 3: Cross-Brand Branch Filter
```
Authorization: Token {vendor_from_brand_1}
GET /api/v1/vendor/reviews?branch_id=999  # Branch from brand 2
Expected: 200 with empty pagination (no results, not 403 error)
```

### Test Case 4: Date Range Filter
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?date_from=2024-01-01&date_to=2024-01-31
Expected: 200 with reviews created in January 2024
```

### Test Case 5: Rating Range Filter
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?min_rating=4&max_rating=5
Expected: 200 with only 5-star and 4-star reviews
```

### Test Case 6: Has Photos Filter
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?has_photos=true
Expected: 200 with only reviews that have at least one photo
```

### Test Case 7: Keyword Search
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews?keyword=excellent
Expected: 200 with reviews matching 'excellent' in comment, user name, or branch name
```

### Test Case 8: View Review Details
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews/42
Expected: 200 with full review including photos URLs and rating answers
Schema: [id, overall_rating, review_score, comment, user, branch, place, photos, answers, created_at]
```

### Test Case 9: Non-Existent Review
```
Authorization: Token {admin_token}
GET /api/v1/vendor/reviews/9999
Expected: 404 Review not found
```

### Test Case 10: BRANCH_STAFF Denied Access
```
Authorization: Token {staff_token}
GET /api/v1/vendor/reviews
Expected: 403 Forbidden (VendorPermissionWithScoping enforces ADMIN role)
```

---

## Error Handling

**Authorization Errors (403):**
- Thrown by `VendorPermissionWithScoping` when user role doesn't match required 'vendor.reviews.list'
- BRANCH_STAFF users cannot access reviews endpoints

**Not Found Errors (404):**
- Review ID doesn't exist
- Review belongs to different brand (filtered out by scoping query)

**Validation Errors (422):**
- Invalid date format
- Invalid rating range (< 0 or > 5)
- Branch ID doesn't exist in database
- per_page exceeds 200

**Response Format:**
```json
{
  "success": false,
  "message": "error_key",
  "data": null,
  "meta": null
}
```

---

## Dependencies & Requirements

**Framework:** Laravel 12  
**Authentication:** Sanctum multi-guard (vendor guard)  
**Models:** Review, ReviewPhoto, ReviewAnswer, Branch, Place, VendorUser  
**Traits:** VendorScoping  
**Middleware:** VendorAuthenticate, VendorPermissionWithScoping  
**Resources:** ReviewPhotoResource (reused from User module), ReviewAnswerResource  

**No Database Migrations Required** - All tables already exist in schema:
- reviews (with place_id, branch_id, overall_rating)
- review_photos (with storage_path)
- review_answers (with criteria_id, choice_id, rating_value, yes_no_value)

---

## Architecture Notes

### Separation of Concerns
- **Request** - Input validation (filters, pagination)
- **Service** - Query building, brand scoping, business logic
- **Controller** - HTTP mapping, error handling, response transformation
- **Resource** - Output serialization (fields selection, transformations)
- **Routes** - Endpoint definition and middleware binding

### Authorization Defense-in-Depth
1. **Middleware**: VendorPermissionWithScoping (role enforcement)
2. **Service Layer**: whereHas('place', ...) ensures brand scoping
3. **Database**: SQL WHERE clause prevents cross-brand access

### Performance Optimizations
- `withCount(['photos'])` - Avoids loading photo collections
- Selective field loading: `:id,full_name,phone,nickname` - Reduces data transfer
- `orderBy('created_at', 'desc')` - Uses indexed column
- Pagination default 15, max 200 - Prevents resource exhaustion

### Reusability
- VendorReviewService can be used for:
  - Email notifications (new reviews)
  - Dashboard analytics (review counts)
  - Export reports
- Resources can be combined in future endpoints

---

## Response Examples

### List Endpoint (200 OK)
```json
{
  "success": true,
  "message": "vendor.reviews.list",
  "data": [
    {"id": 42, "overall_rating": 4.5, ...},
    {"id": 41, "overall_rating": 3.0, ...}
  ],
  "meta": {
    "pagination": {
      "total": 156,
      "count": 2,
      "per_page": 2,
      "current_page": 1,
      "last_page": 78
    }
  }
}
```

### Detail Endpoint (200 OK)
```json
{
  "success": true,
  "message": "vendor.reviews.details",
  "data": {
    "id": 42,
    "overall_rating": 4.5,
    "photos": [...],
    "answers": [...]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "vendor.reviews.not_found",
  "data": null
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "auth.unauthorized",
  "data": null
}
```

---

## Integration Notes

### With Existing Admin Reviews
- Uses same Review model, ReviewPhoto, ReviewAnswer
- Reuses ReviewPhotoResource and ReviewAnswerResource from User module
- Different filters (admin sees is_hidden, is_featured; vendor sees only public)
- Vendor view is read-only (no moderation capabilities)

### With Branch Settings (PROMPT 4)
- Both endpoints use VendorAuthenticate middleware
- Both enforce vendor's brand-level access via relationships
- Vendor can check branch review cooldown while viewing reviews

### With Future Dashboard (PROMPT 6)
- VendorReviewService.list() can be aggregated:
  - Total review count
  - Average rating
  - Photo count
  - Reviews by branch

---

## Next Steps (PROMPT 5b onwards)

Following reviews implementation:
1. **PROMPT 5b: Voucher Verification** - Implement verification endpoints for BRANCH_STAFF
2. **PROMPT 6: Vendor Dashboard** - KPI summary using Review aggregation
3. **PROMPT 7: Vendor Notifications** - Review notification retrieval
4. **PROMPT 8: RBAC Seeding** - Add vendor.reviews.list permission to roles
5. **PROMPT 9: Testing & Polish** - API documentation and validation

---

**Summary:** Vendor reviews listing implemented with comprehensive filtering, brand-level scoping, and full detail view. All reviews isolated by brand to prevent cross-vendor data access. Ready for dashboard integration and testing.
