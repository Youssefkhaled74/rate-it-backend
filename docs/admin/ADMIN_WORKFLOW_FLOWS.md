# Admin Module - Workflow & Flow Diagrams

## Complete Admin Request Flow

```
┌────────────────────────────────────────────────────────────────────┐
│                    ADMIN API REQUEST FLOW                          │
└────────────────────────────────────────────────────────────────────┘

CLIENT REQUEST
    │
    ├─ Send HTTP Request with Authorization Header
    │  Header: Authorization: Bearer {token}
    │
    ▼
REQUEST PIPELINE
    │
    ├─ Match route in routes/api.php
    │  (Route is under /api/v1/admin)
    │
    ▼
MIDDLEWARE STACK
    │
    ├─ AdminAuthenticate Middleware
    │  │
    │  ├─ Extract token from header
    │  ├─ Query personal_access_tokens table
    │  ├─ Load associated admin
    │  ├─ Attach admin to request ($request->user('admin'))
    │  │
    │  ├─ ✓ If valid → Continue
    │  ├─ ✗ If invalid → Return 401 Unauthorized
    │  │
    │  ▼
    │
    ├─ AdminPermission Middleware (if route requires)
    │  │
    │  ├─ Get required permission from route definition
    │  ├─ Load admin's roles from model_has_roles
    │  ├─ Load permissions for each role from role_has_permissions
    │  ├─ Flatten all permissions into collection
    │  │
    │  ├─ Check if required permission exists in collection
    │  │
    │  ├─ ✓ If permission exists → Continue
    │  ├─ ✗ If permission missing → Return 403 Forbidden
    │  │
    │  ▼
    │
    ├─ Controller Method
    │  │
    │  ├─ Execute business logic
    │  ├─ Access database
    │  ├─ Transform data with Resources
    │  │
    │  ▼
    │
    ├─ Response Middleware
    │  │
    │  ├─ Format response (success, message, data, meta)
    │  ├─ Set HTTP status code
    │  ├─ Add headers
    │  │
    │  ▼
    │
CLIENT RECEIVES RESPONSE
    │
    ├─ Status code: 200 OK, 201 Created, 400 Bad Request, etc.
    ├─ Body: JSON response
    │  {
    │    "success": true/false,
    │    "message": "...",
    │    "data": {...},
    │    "meta": {...}
    │  }
    │
    ▼
DONE
```

---

## Authentication Flow - Login

```
┌────────────────────────────────────────────────────────────────────┐
│                    ADMIN LOGIN FLOW                                │
└────────────────────────────────────────────────────────────────────┘

STEP 1: CLIENT REQUEST
┌──────────────────────────────────────────┐
│ POST /api/v1/admin/auth/login            │
│ {                                        │
│   "email": "admin@example.com",          │
│   "password": "password123"              │
│ }                                        │
└──────────────────────────────────────────┘
              │
              ▼

STEP 2: VALIDATION
┌──────────────────────────────────────────┐
│ AuthController::login()                  │
│ ├─ Validate email format                 │
│ ├─ Validate password format              │
│ └─ Return 422 if invalid                 │
└──────────────────────────────────────────┘
              │
              ▼

STEP 3: ADMIN LOOKUP
┌──────────────────────────────────────────┐
│ Find Admin by email                      │
│ SELECT * FROM admins WHERE email = ?     │
│                                          │
│ ├─ ✓ Found → Continue                    │
│ └─ ✗ Not found → Return 401              │
└──────────────────────────────────────────┘
              │
              ▼

STEP 4: IS_ACTIVE CHECK
┌──────────────────────────────────────────┐
│ Check if admin->is_active == true        │
│                                          │
│ ├─ ✓ Active → Continue                   │
│ └─ ✗ Inactive → Return 401               │
└──────────────────────────────────────────┘
              │
              ▼

STEP 5: PASSWORD VERIFICATION
┌──────────────────────────────────────────┐
│ Hash::check(input_password,              │
│   admin->password_hash)                  │
│                                          │
│ ├─ ✓ Matches → Continue                  │
│ └─ ✗ Doesn't match → Return 401          │
└──────────────────────────────────────────┘
              │
              ▼

STEP 6: ROLE CHECK
┌──────────────────────────────────────────┐
│ Load admin->roles()                      │
│ SELECT roles.* FROM roles                │
│ INNER JOIN model_has_roles               │
│                                          │
│ ├─ ✓ Has roles → Continue                │
│ └─ ✗ No roles → Return 401               │
└──────────────────────────────────────────┘
              │
              ▼

STEP 7: PERMISSION COLLECTION
┌──────────────────────────────────────────┐
│ admin->permissions()                     │
│ Flatten all permissions from all roles   │
│ SELECT permissions.* FROM permissions    │
│ INNER JOIN role_has_permissions          │
│                                          │
│ Return: Collection<Permission>           │
└──────────────────────────────────────────┘
              │
              ▼

STEP 8: TOKEN GENERATION
┌──────────────────────────────────────────┐
│ Sanctum::createToken()                   │
│ ├─ Generate unique token                 │
│ ├─ Store in personal_access_tokens table │
│ │  - tokenable_id: admin.id              │
│ │  - tokenable_type: App\Models\Admin    │
│ │  - token (hashed)                      │
│ │  - abilities: ['*']                    │
│ │  - created_at                          │
│ └─ Return: AccessToken object            │
└──────────────────────────────────────────┘
              │
              ▼

STEP 9: RESOURCE FORMATTING
┌──────────────────────────────────────────┐
│ AdminResource::toArray()                 │
│ ├─ Admin data                            │
│ ├─ Formatted roles                       │
│ ├─ Formatted permissions                 │
│ ├─ Timestamp objects                     │
│ └─ Exclude: password_hash                │
└──────────────────────────────────────────┘
              │
              ▼

STEP 10: RESPONSE
┌──────────────────────────────────────────┐
│ HTTP 200 OK                              │
│ {                                        │
│   "success": true,                       │
│   "message": "Login successful",         │
│   "data": {                              │
│     "admin": {...},                      │
│     "token": "1|abc123..."               │
│   }                                      │
│ }                                        │
└──────────────────────────────────────────┘
              │
              ▼

STEP 11: CLIENT STORES TOKEN
┌──────────────────────────────────────────┐
│ Save token in localStorage               │
│ Or session storage                       │
│ Use in all future requests:              │
│ Authorization: Bearer {token}            │
└──────────────────────────────────────────┘
```

---

## Permission Check Flow

```
┌────────────────────────────────────────────────────────────────────┐
│               ADMIN PERMISSION CHECK FLOW                          │
└────────────────────────────────────────────────────────────────────┘

REQUEST ARRIVES
    │
    ├─ Route: PUT /api/v1/admin/categories/1
    ├─ Requires: catalog.categories.update
    │
    ▼

ADMINPERMISSION MIDDLEWARE
    │
    ├─ Extract permission from route definition
    │  permission = 'catalog.categories.update'
    │
    ▼

LOAD ADMIN
    │
    ├─ From: $request->user('admin')
    ├─ Already authenticated by AdminAuthenticate
    │
    ▼

LOAD ROLES
    │
    ├─ Query: model_has_roles
    ├─ Where: model_id = admin.id
    │  and model_type = 'App\Models\Admin'
    │
    ▼

LOAD PERMISSIONS FOR EACH ROLE
    │
    ├─ For each role:
    │  │
    │  ├─ Query: role_has_permissions
    │  ├─ Where: role_id = role.id
    │  │
    │  ├─ Join: permissions table
    │  │
    │  ├─ Collect: permission names
    │  │
    │  ▼
    │
    ├─ Merge all permissions into collection
    │
    ▼

PERMISSION ARRAY
    │
    ├─ [
    │     "admin.auth.login",
    │     "catalog.categories.view",
    │     "catalog.categories.create",
    │     "catalog.categories.update",  ← Looking for this
    │     "catalog.categories.delete",
    │     "dashboard.view",
    │     ...
    │   ]
    │
    ▼

CHECK REQUIRED PERMISSION
    │
    ├─ Is 'catalog.categories.update' in array?
    │
    ├─ ✓ YES → Continue to controller
    │
    ├─ ✗ NO → Return 403 Forbidden
    │         {
    │           "success": false,
    │           "message": "Unauthorized",
    │           "status": 403
    │         }
    │
    ▼

CONTROLLER EXECUTES
    │
    ├─ Full permission to update category
    ├─ Access database
    ├─ Return updated category
    │
    ▼

DONE
```

---

## Catalog Management Workflow

```
┌────────────────────────────────────────────────────────────────────┐
│          COMPLETE CATALOG SETUP WORKFLOW                           │
└────────────────────────────────────────────────────────────────────┘

PHASE 1: HIERARCHY SETUP
┌──────────────────────────────────────┐
│ 1. Create Categories                 │
│    POST /categories                  │
│    {                                 │
│      name_en: "Restaurants",          │
│      name_ar: "المطاعم"              │
│    }                                 │
│    ✓ Returns: category_id = 1        │
└──────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────┐
│ 2. Create Subcategories              │
│    POST /subcategories               │
│    {                                 │
│      category_id: 1,                 │
│      name_en: "Italian",             │
│      name_ar: "إيطالي"               │
│    }                                 │
│    ✓ Returns: subcategory_id = 4     │
└──────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────┐
│ 3. Create Brands (optional)          │
│    POST /brands                      │
│    {                                 │
│      name_en: "Brand Name",          │
│      name_ar: "اسم العلامة"          │
│    }                                 │
│    ✓ Returns: brand_id = 2           │
└──────────────────────────────────────┘
         │
         ▼

PHASE 2: RATING SETUP
┌──────────────────────────────────────┐
│ 4. Create Rating Criteria            │
│    POST /rating-criteria             │
│    {                                 │
│      subcategory_id: 4,              │
│      name_en: "Cleanliness",         │
│      name_ar: "النظافة",             │
│      type: "RATING"                  │
│    }                                 │
│    ✓ Returns: criteria_id = 15       │
└──────────────────────────────────────┘
         │
         ├─ Type: RATING
         │  └─ No choices needed
         │
         ├─ Type: YES_NO
         │  └─ No choices needed
         │
         └─ Type: MULTIPLE_CHOICE
            └─ Continue to Step 5
            │
            ▼
┌──────────────────────────────────────┐
│ 5. Add Choices                       │
│    POST /rating-criteria/15/choices  │
│    {                                 │
│      name_en: "Excellent",           │
│      name_ar: "ممتاز",               │
│      value: 5                        │
│    }                                 │
│    ✓ Returns: choice_id = 47         │
│                                      │
│    Repeat for: "Good", "Fair",       │
│    "Poor", "Terrible"                │
└──────────────────────────────────────┘
         │
         ▼

PHASE 3: BUSINESS SETUP
┌──────────────────────────────────────┐
│ 6. Create Places                     │
│    POST /places                      │
│    {                                 │
│      subcategory_id: 4,              │
│      brand_id: 2,                    │
│      name_en: "Ristorante",          │
│      name_ar: "مطعم",                │
│      location_data: {...}            │
│    }                                 │
│    ✓ Returns: place_id = 23          │
└──────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────┐
│ 7. Create Branches (if multi-branch) │
│    POST /branches                    │
│    {                                 │
│      place_id: 23,                   │
│      name_en: "Branch 1",            │
│      name_ar: "الفرع 1",             │
│      location_data: {...}            │
│    }                                 │
│    ✓ Returns: branch_id = 89         │
└──────────────────────────────────────┘
         │
         ▼

COMPLETE
    │
    └─ Users can now:
       • View categories and subcategories
       • Browse places and branches
       • Submit reviews with rating criteria
       • Rate on RATING (1-5)
       • Answer YES_NO questions
       • Choose from MULTIPLE_CHOICE options
```

---

## Data Model Relationships

```
┌────────────────────────────────────────────────────────────────────┐
│                 ADMIN MODULE DATA MODEL                            │
└────────────────────────────────────────────────────────────────────┘

AUTHENTICATION & AUTHORIZATION
┌─────────────┐
│   Admin     │─┐
├─────────────┤ │
│ id          │ │
│ email       │ │
│ password    │ │
│ is_active   │ │
└─────────────┘ │
                │  (morphToMany)
                ├─────────────────────────┐
                │                         │
                ▼                         ▼
         ┌──────────────┐         ┌──────────────────┐
         │ model_has    │         │ Role             │
         │ _roles       │─────────│──────────────────│
         ├──────────────┤  1:N    │ id               │
         │ model_id     │         │ name             │
         │ model_type   │         │ guard            │
         │ role_id      │         │ description      │
         └──────────────┘         └──────────────────┘
                                          │
                                          │ (belongsToMany)
                                          │
                                          ▼
                                  ┌──────────────────┐
                                  │ role_has         │
                                  │ _permissions     │
                                  ├──────────────────┤
                                  │ role_id          │
                                  │ permission_id    │
                                  └──────────────────┘
                                          │
                                          │ (hasMany)
                                          │
                                          ▼
                                  ┌──────────────────┐
                                  │ Permission       │
                                  ├──────────────────┤
                                  │ id               │
                                  │ name             │
                                  │ guard            │
                                  │ description      │
                                  └──────────────────┘

CATALOG HIERARCHY
┌──────────────┐         ┌───────────────┐
│ Category     │────────┤ Subcategory   │
├──────────────┤   1:N   ├───────────────┤
│ id           │         │ id            │
│ name_en      │         │ category_id   │
│ name_ar      │         │ name_en       │
│ sort_order   │         │ name_ar       │
└──────────────┘         │ sort_order    │
                         └───────────────┘
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
                    ▼            ▼            ▼
            ┌──────────────┐  ┌────────────┐  ┌──────────────┐
            │ Place        │  │ Brand      │  │ RatingCriteria
            ├──────────────┤  ├────────────┤  ├──────────────┤
            │ id           │  │ id         │  │ id           │
            │ brand_id (FK)├──│ id         │  │ subcategory..│
            │ name_en      │  │ name_en    │  │ question_en  │
            │ name_ar      │  │ name_ar    │  │ question_ar  │
            │ location     │  └────────────┘  │ type         │
            └──────────────┘                  │ is_required  │
                    │                         └──────────────┘
                    │ (1:N)                           │
                    │                          (1:N)  │
                    ▼                                 │
            ┌──────────────┐                        ▼
            │ Branch       │               ┌──────────────────┐
            ├──────────────┤               │ RatingCriteria   │
            │ id           │               │ Choice           │
            │ place_id     │               ├──────────────────┤
            │ name_en      │               │ id               │
            │ name_ar      │               │ criteria_id      │
            │ location     │               │ choice_en        │
            └──────────────┘               │ choice_ar        │
                                           │ value            │
                                           └──────────────────┘
```

---

## Response Format Standard

```json
{
  "success": true|false,
  "message": "User-friendly message",
  "data": {
    // Any data returned from endpoint
    "id": 1,
    "name": "...",
    "timestamps": {
      "created_at": {
        "iso": "2026-01-21T14:13:38.000000Z",
        "readable": "Jan 21, 2026 14:13:38",
        "relative": "3 days ago",
        "unix": 1769004818,
        "date": "2026-01-21",
        "time": "14:13:38"
      },
      "updated_at": {...}
    }
  },
  "meta": {
    // Pagination info, counts, etc.
    "total": 100,
    "per_page": 15,
    "current_page": 1
  }
}
```

---

## Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "data": null,
  "meta": null
}
```

Status Codes:
- `200` - Success
- `201` - Created
- `204` - No Content
- `400` - Bad Request (validation error)
- `401` - Unauthorized (invalid/expired token)
- `403` - Forbidden (missing permission)
- `404` - Not Found
- `422` - Unprocessable Entity (validation failed)
- `500` - Server Error
