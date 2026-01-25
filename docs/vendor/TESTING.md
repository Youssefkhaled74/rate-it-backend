# Admin API Test Suite

Complete automated test suite for the Admin module with 50+ feature tests covering all admin endpoints, business rules, and RBAC permissions.

## Quick Start

### Run All Tests
```bash
composer test
```

### Run Only Admin Tests
```bash
composer test:admin
```

### Run Admin Tests with Verbose Output
```bash
composer test:admin:verbose
```

### Run Specific Test File
```bash
php artisan test tests/Feature/Admin/Auth/AuthTest.php
```

### Run Specific Test Class
```bash
php artisan test --filter AuthTest
```

### Run Specific Test Method
```bash
php artisan test --filter test_admin_login_with_valid_credentials
```

## Test Structure

Tests are organized by module under `tests/Feature/Admin/`:

```
tests/Feature/Admin/
├── Support/
│   ├── AdminTestCase.php          # Base test class with helpers
│   └── InteractsWithAdminApi.php  # API interaction trait
├── Auth/
│   └── AuthTest.php                # Login, logout, authentication
├── Rbac/
│   └── RolesTest.php               # Roles, permissions, RBAC
├── Catalog/
│   ├── CategoriesTest.php          # Category CRUD
│   ├── SubcategoriesTest.php       # Subcategory CRUD
│   ├── BrandsTest.php              # Brand CRUD
│   ├── PlacesTest.php              # Place CRUD + relationships
│   ├── BranchesTest.php            # Branch CRUD + QR codes
│   └── RatingCriteriaTest.php      # Rating criteria + choices
├── CatalogIntegrity/
│   └── CatalogIntegrityTest.php    # Data consistency validation
├── Reviews/
│   └── ReviewsModerationTest.php   # Review moderation
├── Users/
│   └── UsersManagementTest.php     # User management, blocking
├── Points/
│   └── PointsTransactionsTest.php  # Points monitoring
├── LoyaltySettings/
│   └── LoyaltySettingsTest.php     # Immutable versioned settings
├── Notifications/
│   └── NotificationsAdminTest.php  # Templates, broadcast, send
├── Invites/
│   └── InvitesAdminTest.php        # Invite list and filtering
├── Subscriptions/
│   └── SubscriptionPlansTest.php   # Plans and subscriptions
└── Dashboard/
    └── DashboardTest.php            # Analytics KPIs and charts
```

## Test Coverage

### Auth & RBAC (15 tests)
- ✅ Login with valid credentials
- ✅ Login validation (invalid email, wrong password, inactive admin)
- ✅ Me endpoint (authenticated details)
- ✅ Logout and token invalidation
- ✅ Role listing and management
- ✅ Permission listing and creation
- ✅ Permission syncing to roles

### Catalog (35+ tests)
- ✅ Categories CRUD + filtering
- ✅ Subcategories CRUD with category relationships
- ✅ Brands CRUD
- ✅ Places CRUD + location validation
- ✅ Place-Brand relationships (Task 02.1)
- ✅ Place-Category/Subcategory relationships (Task 02.2)
- ✅ Branches CRUD with QR code management
- ✅ Branch QR code uniqueness constraints
- ✅ Branch QR regeneration
- ✅ Rating Criteria CRUD with type validation
- ✅ Rating Criteria Choices with unique value constraints

### Catalog Integrity (4 tests)
- ✅ Validate place-brand relationships
- ✅ Validate place category/subcategory relationships
- ✅ Validate branch QR codes
- ✅ Fix integrity issues

### Reviews (6 tests)
- ✅ List reviews with pagination
- ✅ Show review details
- ✅ Hide reviews
- ✅ Reply to reviews
- ✅ Mark as featured

### Users (8 tests)
- ✅ List users with search and pagination
- ✅ Show user details
- ✅ Block/unblock users
- ✅ Get user reviews
- ✅ Get user points balance and history

### Points (6 tests)
- ✅ List transactions with pagination
- ✅ Filter by type and user
- ✅ Show transaction details

### Loyalty Settings (6 tests)
- ✅ List versioned settings
- ✅ Create new version
- ✅ Activate version
- ✅ Immutability enforcement (no direct update/delete)

### Notifications (5 tests)
- ✅ List/create templates
- ✅ Broadcast to all users
- ✅ Send to specific user

### Invites (5 tests)
- ✅ List with pagination
- ✅ Filter by status
- ✅ Show invite details

### Subscriptions (8 tests)
- ✅ Plans CRUD and activation
- ✅ Subscriptions list and filtering

### Dashboard (6 tests)
- ✅ Summary KPIs (users, reviews, avg rating, points)
- ✅ Top places by reviews count
- ✅ Top places by average rating
- ✅ Reviews chart (timeseries)
- ✅ Custom date range filtering

## Test Helpers

### AdminTestCase Class

Base test class extending Laravel TestCase with:

- **setUp()**: Automatically seeds test database with admin user and catalog data
- **loginAsAdmin($email, $password)**: Login and store token
- **adminHeaders()**: Get auth headers for requests
- **adminHeadersWithToken($token)**: Headers with custom token

### Assertion Methods

- **assertSuccessJson($response)**: Verify 200 response with success=true
- **assertCreatedJson($response)**: Verify 201 response
- **assertUnauthorizedJson($response)**: Verify 401 response
- **assertForbiddenJson($response)**: Verify 403 response
- **assertValidationErrorJson($response)**: Verify 422 response
- **assertNotFoundJson($response)**: Verify 404 response

### Request Methods

- **getAsAdmin($uri)**: GET with auth
- **postAsAdmin($uri, $data)**: POST with auth
- **putAsAdmin($uri, $data)**: PUT with auth
- **patchAsAdmin($uri, $data)**: PATCH with auth
- **deleteAsAdmin($uri, $data)**: DELETE with auth
- **getAsGuest($uri)**: GET without auth
- **postAsGuest($uri, $data)**: POST without auth

## Test Environment

Tests use:
- **Database**: SQLite in-memory (fast, isolated)
- **Seeder**: `AdminTestSeeder` creates:
  - Super admin user (email: admin@test.local, password: password)
  - Test admin user (email: test.admin@test.local, password: password)
  - All roles and permissions
  - Catalog data (categories, brands, places, branches, rating criteria)

## Configuration

### phpunit.xml

Already configured with:
- SQLite in-memory database
- RefreshDatabase trait (automatic migrations)
- Test environment variables (CACHE_STORE=array, MAIL_MAILER=array, etc.)

### AdminTestSeeder (database/seeders/AdminTestSeeder.php)

Creates test data:
- Admin users with SUPER_ADMIN and ADMIN roles
- All system roles and permissions
- Test catalog items with proper relationships

## Output Examples

### Success
```
✅ ADMIN API TESTS PASSED — EVERYTHING OK
```

### Failure
```
❌ ADMIN API TESTS FAILED — CHECK FAILURES ABOVE
```

## Common Issues

### "Column not found" errors
Run migrations:
```bash
php artisan migrate --force
```

### Token authentication issues
Ensure AdminTestSeeder creates admin users correctly. Check:
```bash
php artisan tinker
>>> DB::table('admins')->where('email', 'admin@test.local')->first();
```

### Seeding failures
Clear cache and retry:
```bash
php artisan cache:clear
composer dump-autoload
composer test:admin
```

## Continuous Integration

For CI/CD pipelines:
```bash
# Quick health check
php artisan test --testsuite=Feature --filter=Auth --failOnWarning

# Full suite with reporting
php artisan test --testsuite=Feature --filter=Admin --teamcity

# Coverage report
php artisan test --coverage --coverage-html=coverage/
```

## Adding New Tests

1. Create test file in appropriate `tests/Feature/Admin/{Module}/` directory
2. Extend `AdminTestCase`
3. Use helper methods for requests and assertions
4. Run specific test:
   ```bash
   php artisan test tests/Feature/Admin/{Module}/{NewTest}.php
   ```

## Performance

Full test suite runs in ~15-30 seconds depending on system:
- ~50 tests
- In-memory SQLite (very fast)
- Parallel-safe (RefreshDatabase per test)
- No network calls

## Support

- Tests follow Laravel conventions
- Responses match API response format: `{ success: bool, message: string, data: any, meta: any }`
- All tests are deterministic (no random failures)
- Use `--verbose` flag for detailed output during development
