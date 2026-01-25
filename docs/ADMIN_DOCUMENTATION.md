# Admin Module Documentation

## Table of Contents
1. [Overview](#overview)
2. [Admin Architecture](#admin-architecture)
3. [Authentication & Authorization](#authentication--authorization)
4. [Admin Lifecycle & Workflow](#admin-lifecycle--workflow)
5. [API Endpoints](#api-endpoints)
6. [Admin Roles & Permissions](#admin-roles--permissions)
7. [Database Schema](#database-schema)
8. [Usage Examples](#usage-examples)

---

## Overview

The Admin Module is a comprehensive management system for administering the Rate-It platform. It provides:

- **User Authentication**: Secure login with Sanctum tokens
- **Role-Based Access Control (RBAC)**: Fine-grained permission management
- **Catalog Management**: Categories, subcategories, brands, places, branches
- **Rating Criteria Management**: Questions and choices with bilingual support (English/Arabic)
- **Review Management**: Monitor and manage user reviews
- **Dashboard Analytics**: KPI tracking and performance metrics
- **User Management**: Admin user administration
- **Notifications**: System notifications and alerts
- **Points & Loyalty**: Point settings and transaction tracking
- **Subscriptions**: Subscription plan management
- **Invitations**: User invitation system

---

## Admin Architecture

### Core Models

#### Admin Model
```php
// app/Models/Admin.php
- id: Primary key
- name: Admin's full name
- email: Unique email address
- phone: Contact phone number
- password_hash: Hashed password
- is_active: Boolean flag for account status
- timestamps: created_at, updated_at, deleted_at (soft delete)

// Relationships
- roles(): morphToMany Role (RBAC)
- permissions(): Flattened collection of all role permissions
```

#### Role Model
```php
// app/Models/Role.php
- id: Primary key
- name: Role name (e.g., 'Super Admin', 'Category Manager')
- guard: Guard name (e.g., 'admin')
- description: Role description
- timestamps: created_at, updated_at

// Relationships
- permissions(): belongsToMany Permission
```

#### Permission Model
```php
// app/Models/Permission.php
- id: Primary key
- name: Permission key (e.g., 'admin.auth.login', 'catalog.categories.create')
- guard: Guard name (e.g., 'admin')
- description: Permission description
- timestamps: created_at, updated_at
```

### Authentication System

**Guard Configuration** (`config/auth.php`):
```php
'guards' => [
    'admin' => [
        'driver' => 'sanctum',
        'provider' => 'admins',
    ],
],
'providers' => [
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
]
```

**Middleware**:
- `AdminAuthenticate`: Validates Sanctum token for admin
- `AdminPermission`: Checks admin has required permission

---

## Authentication & Authorization

### Login Flow

1. **Client sends credentials**:
```http
POST /api/v1/admin/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password123"
}
```

2. **Server validates**:
   - Verifies email exists
   - Checks `is_active` status
   - Verifies password hash
   - Checks admin has roles

3. **Server returns**:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "phone": "+966501234567",
        "is_active": true,
        "roles": [
            {
                "id": 1,
                "name": "Super Admin",
                "description": "Full system access"
            }
        ],
        "permissions": [
            {
                "id": 1,
                "name": "admin.auth.login",
                "description": "Can login to admin panel"
            },
            ...
        ],
        "timestamps": {
            "created_at": {...},
            "updated_at": {...}
        }
    }
}
```

4. **Client stores token** in Authorization header:
```
Authorization: Bearer {token}
```

### Logout Flow

```http
POST /api/v1/admin/auth/logout
Authorization: Bearer {token}
```

Revokes the current token from `personal_access_tokens` table.

### Get Current Admin

```http
GET /api/v1/admin/auth/me
Authorization: Bearer {token}
```

---

## Admin Lifecycle & Workflow

### Complete Admin Cycle

```
┌─────────────────────────────────────────────────────────────────┐
│                    ADMIN LIFECYCLE                              │
└─────────────────────────────────────────────────────────────────┘

1. ADMIN CREATION
   ├─ Database Admin record created
   ├─ Password hashed and stored
   ├─ is_active set to true/false
   └─ Timestamps recorded

2. ROLE ASSIGNMENT
   ├─ Admin assigned to one or more roles
   ├─ Roles stored in model_has_roles pivot table
   └─ Admin inherits all role permissions

3. PERMISSION EVALUATION
   ├─ On each request, AdminPermission middleware checks:
   │  ├─ Is admin authenticated? (AdminAuthenticate)
   │  ├─ Does admin have required permission?
   │  └─ Return 403 Forbidden if unauthorized
   └─ Permissions are flattened from all roles

4. FEATURE USAGE
   ├─ Admin can access catalogs (categories, subcategories, etc.)
   ├─ Admin can manage rating criteria and choices
   ├─ Admin can view reviews and analytics
   ├─ Admin can manage user invitations
   ├─ Admin can configure points and loyalty
   └─ Admin can manage subscriptions

5. DEACTIVATION
   ├─ is_active set to false
   ├─ Admin cannot login
   ├─ Existing tokens remain valid
   └─ Account data preserved (soft delete support)

6. DELETION
   ├─ Admin record soft-deleted
   ├─ Record not visible in queries by default
   ├─ Can be restored via restore()
   └─ Permanent deletion available if needed
```

### Permission Check Flow

```
┌──────────────────────────────────────────────────────────────┐
│               PERMISSION CHECK FLOW                          │
└──────────────────────────────────────────────────────────────┘

Request with Authorization Header
           ↓
    AdminAuthenticate Middleware
      ├─ Decode Sanctum token
      ├─ Load admin from personal_access_tokens
      └─ Attach admin to request context
           ↓
    AdminPermission Middleware
      ├─ Get required permission from route
      ├─ Load admin's roles
      ├─ Flatten all permissions from roles
      ├─ Check if permission exists in flattened list
      └─ Return 403 if permission missing
           ↓
    Route Handler Executes
      ├─ Request fully authorized
      └─ Response generated
```

---

## API Endpoints

### Authentication Endpoints

#### 1. Login
```http
POST /api/v1/admin/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password123"
}

Response: 200 OK
{
    "success": true,
    "message": "Login successful",
    "data": {
        "admin": {...},
        "token": "..."
    }
}
```

#### 2. Logout (Protected)
```http
POST /api/v1/admin/auth/logout
Authorization: Bearer {token}

Response: 204 No Content
```

#### 3. Get Current Admin (Protected)
```http
GET /api/v1/admin/auth/me
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": {...}
}
```

### Dashboard Endpoints (Protected: `dashboard.view`)

#### 1. Summary Statistics
```http
GET /api/v1/admin/dashboard/summary
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": {
        "total_reviews": 1234,
        "total_places": 56,
        "average_rating": 4.5,
        "active_users": 789
    }
}
```

#### 2. Top Places
```http
GET /api/v1/admin/dashboard/top-places
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Restaurant ABC",
            "rating": 4.8,
            "review_count": 156
        },
        ...
    ]
}
```

#### 3. Reviews Chart
```http
GET /api/v1/admin/dashboard/reviews-chart
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": {
        "labels": ["Jan", "Feb", "Mar", ...],
        "datasets": [
            {
                "label": "Reviews",
                "data": [45, 67, 89, ...]
            }
        ]
    }
}
```

### Catalog Endpoints (Protected)

#### Categories

**List**
```http
GET /api/v1/admin/categories
Authorization: Bearer {token}

Query Parameters:
- active=true/false (optional)
- sort_by=name (optional)

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name_en": "Restaurants",
            "name_ar": "المطاعم",
            "logo": "url...",
            "is_active": true,
            "sort_order": 0,
            "timestamps": {...}
        },
        ...
    ]
}
```

**Create**
```http
POST /api/v1/admin/categories
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Restaurants",
    "name_ar": "المطاعم",
    "logo": "url...",
    "is_active": true,
    "sort_order": 0
}

Response: 201 Created
{
    "success": true,
    "message": "Category created successfully",
    "data": {...}
}
```

**Update**
```http
PUT /api/v1/admin/categories/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Restaurants Updated",
    "name_ar": "المطاعم المحدثة",
    "is_active": true
}

Response: 200 OK
```

**Delete**
```http
DELETE /api/v1/admin/categories/{id}
Authorization: Bearer {token}

Response: 204 No Content
```

#### Subcategories

**List**
```http
GET /api/v1/admin/subcategories?category_id=1
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "category_id": 1,
            "name_en": "Italian",
            "name_ar": "إيطالي",
            "image": "url...",
            "is_active": true,
            "sort_order": 0,
            "timestamps": {...}
        },
        ...
    ]
}
```

**Create**
```http
POST /api/v1/admin/subcategories
Authorization: Bearer {token}
Content-Type: application/json

{
    "category_id": 1,
    "name_en": "Italian",
    "name_ar": "إيطالي",
    "image": "url...",
    "is_active": true,
    "sort_order": 0
}

Response: 201 Created
```

**Update, Show, Delete**: Similar pattern to Categories

#### Brands

**CRUD Operations**:
```http
GET    /api/v1/admin/brands
POST   /api/v1/admin/brands
GET    /api/v1/admin/brands/{id}
PUT    /api/v1/admin/brands/{id}
DELETE /api/v1/admin/brands/{id}

Fields:
- name_en, name_ar
- description_en, description_ar
- logo
- is_active
- sort_order
```

#### Places

**CRUD Operations**:
```http
GET    /api/v1/admin/places
POST   /api/v1/admin/places
GET    /api/v1/admin/places/{id}
PUT    /api/v1/admin/places/{id}
DELETE /api/v1/admin/places/{id}

Fields:
- name_en, name_ar
- description_en, description_ar
- image
- subcategory_id
- brand_id
- is_active
- location_data (coordinates, address)
```

#### Branches

**CRUD Operations**:
```http
GET    /api/v1/admin/branches
POST   /api/v1/admin/branches
GET    /api/v1/admin/branches/{id}
PUT    /api/v1/admin/branches/{id}
DELETE /api/v1/admin/branches/{id}

Fields:
- place_id
- name_en, name_ar
- phone, email
- location_data
- is_active
```

### Rating Criteria Endpoints (Protected)

#### List Criteria
```http
GET /api/v1/admin/rating-criteria
Authorization: Bearer {token}

Query Parameters:
- subcategory_id=4 (optional)
- type=RATING|YES_NO|MULTIPLE_CHOICE (optional)
- active=true/false (optional)

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name_en": "Cleanliness",
            "name_ar": "النظافة",
            "type": "RATING",
            "is_required": true,
            "is_active": true,
            "sort_order": 0,
            "subcategory": {...},
            "choices": [
                {
                    "id": 1,
                    "name_en": "Poor",
                    "name_ar": "سيء",
                    "value": 1,
                    "sort_order": 0
                },
                ...
            ],
            "review_answers": [],
            "timestamps": {...}
        },
        ...
    ]
}
```

#### Create Criteria
```http
POST /api/v1/admin/rating-criteria
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Cleanliness",
    "name_ar": "النظافة",
    "type": "RATING",
    "subcategory_id": 4,
    "is_required": true,
    "is_active": true,
    "sort_order": 0
}

Response: 201 Created
{
    "success": true,
    "message": "Rating criteria created successfully",
    "data": {...}
}
```

#### Criteria Types

1. **RATING**: Numeric scale (1-5)
2. **YES_NO**: Boolean response
3. **MULTIPLE_CHOICE**: Multiple options with choices

#### Show Criteria
```http
GET /api/v1/admin/rating-criteria/{id}
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": {...}
}
```

#### Update Criteria
```http
PUT /api/v1/admin/rating-criteria/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Cleanliness Updated",
    "name_ar": "النظافة المحدثة",
    "is_active": false
}

Response: 200 OK
```

#### Delete Criteria
```http
DELETE /api/v1/admin/rating-criteria/{id}
Authorization: Bearer {token}

Response: 204 No Content
```

### Rating Criteria Choices Endpoints

#### List Choices
```http
GET /api/v1/admin/rating-criteria/{criteria_id}/choices
Authorization: Bearer {token}

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "rating_criteria_id": 5,
            "name_en": "Poor",
            "name_ar": "سيء",
            "value": 1,
            "is_active": true,
            "sort_order": 0,
            "timestamps": {...}
        },
        ...
    ]
}
```

#### Create Choice
```http
POST /api/v1/admin/rating-criteria/{criteria_id}/choices
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Poor",
    "name_ar": "سيء",
    "value": 1,
    "is_active": true,
    "sort_order": 0
}

Response: 201 Created
```

#### Update Choice
```http
PUT /api/v1/admin/rating-criteria/{criteria_id}/choices/{choice_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name_en": "Bad",
    "name_ar": "سيء جداً",
    "value": 1
}

Response: 200 OK
```

#### Delete Choice
```http
DELETE /api/v1/admin/rating-criteria/{criteria_id}/choices/{choice_id}
Authorization: Bearer {token}

Response: 204 No Content
```

### RBAC Endpoints (Protected)

#### List Roles
```http
GET /api/v1/admin/roles
Authorization: Bearer {token}
Requires Permission: rbac.roles.manage

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Super Admin",
            "guard": "admin",
            "description": "Full system access",
            "permissions": [...]
        },
        ...
    ]
}
```

#### Create Role
```http
POST /api/v1/admin/roles
Authorization: Bearer {token}
Content-Type: application/json
Requires Permission: rbac.roles.manage

{
    "name": "Content Manager",
    "guard": "admin",
    "description": "Can manage catalog content"
}

Response: 201 Created
```

#### Sync Role Permissions
```http
POST /api/v1/admin/roles/{role_id}/sync-permissions
Authorization: Bearer {token}
Content-Type: application/json
Requires Permission: rbac.roles.manage

{
    "permission_ids": [1, 2, 3, ...]
}

Response: 200 OK
{
    "success": true,
    "message": "Permissions synced",
    "data": {...}
}
```

#### List Permissions
```http
GET /api/v1/admin/permissions
Authorization: Bearer {token}
Requires Permission: rbac.permissions.manage

Response: 200 OK
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "admin.auth.login",
            "guard": "admin",
            "description": "Can login to admin panel"
        },
        ...
    ]
}
```

---

## Admin Roles & Permissions

### Predefined Roles

#### 1. Super Admin
- **Description**: Full system access
- **Permissions**: All permissions

#### 2. Category Manager
- **Description**: Manage categories and subcategories
- **Permissions**:
  - `catalog.categories.view`
  - `catalog.categories.create`
  - `catalog.categories.update`
  - `catalog.categories.delete`
  - `catalog.subcategories.view`
  - `catalog.subcategories.create`
  - `catalog.subcategories.update`
  - `catalog.subcategories.delete`

#### 3. Rating Manager
- **Description**: Manage rating criteria
- **Permissions**:
  - `catalog.rating-criteria.view`
  - `catalog.rating-criteria.create`
  - `catalog.rating-criteria.update`
  - `catalog.rating-criteria.delete`
  - `catalog.rating-criteria-choices.manage`

#### 4. Review Moderator
- **Description**: Moderate user reviews
- **Permissions**:
  - `reviews.view`
  - `reviews.approve`
  - `reviews.reject`
  - `reviews.delete`

#### 5. Analytics Viewer
- **Description**: View analytics and reports
- **Permissions**:
  - `dashboard.view`
  - `reports.view`

### Permission Hierarchy

```
Root Permissions:
├── admin
│   ├── auth
│   │   └── login: Login to admin panel
│   └── users
│       ├── view: View admin users
│       ├── create: Create admin users
│       ├── update: Update admin users
│       └── delete: Delete admin users
├── catalog
│   ├── categories
│   │   ├── view
│   │   ├── create
│   │   ├── update
│   │   └── delete
│   ├── subcategories
│   │   ├── view
│   │   ├── create
│   │   ├── update
│   │   └── delete
│   ├── brands
│   │   ├── view
│   │   ├── create
│   │   ├── update
│   │   └── delete
│   ├── places
│   │   ├── view
│   │   ├── create
│   │   ├── update
│   │   └── delete
│   ├── branches
│   │   ├── view
│   │   ├── create
│   │   ├── update
│   │   └── delete
│   └── rating-criteria
│       ├── view
│       ├── create
│       ├── update
│       ├── delete
│       └── choices-manage
├── reviews
│   ├── view: View all reviews
│   ├── approve: Approve pending reviews
│   ├── reject: Reject reviews
│   └── delete: Delete reviews
├── dashboard
│   └── view: View dashboard
├── users
│   ├── view: View regular users
│   ├── block: Block users
│   └── unblock: Unblock users
└── rbac
    ├── roles
    │   └── manage: Manage roles
    └── permissions
        └── manage: Manage permissions
```

---

## Database Schema

### Admins Table
```sql
CREATE TABLE admins (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NULLABLE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULLABLE
);

Indexes:
- email (UNIQUE)
- is_active
- deleted_at
```

### Roles Table
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    guard VARCHAR(255) NOT NULL,
    description TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

Indexes:
- name (UNIQUE)
- guard
```

### Permissions Table
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    guard VARCHAR(255) NOT NULL,
    description TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

Indexes:
- name (UNIQUE)
- guard
```

### Model Has Roles Table (Pivot)
```sql
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED,
    model_id BIGINT UNSIGNED,
    model_type VARCHAR(255),
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

### Role Has Permissions Table (Pivot)
```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED,
    role_id BIGINT UNSIGNED,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

---

## Usage Examples

### Example 1: Complete Admin Login and Feature Access

```php
// 1. Login
$response = Http::post('http://localhost/api/v1/admin/auth/login', [
    'email' => 'admin@example.com',
    'password' => 'password123'
]);

$token = $response['data']['token'];
// Returns admin with roles and permissions

// 2. Access protected resource
$response = Http::withToken($token)->get('http://localhost/api/v1/admin/dashboard/summary');
// Returns KPI data

// 3. Create new category
$response = Http::withToken($token)->post('http://localhost/api/v1/admin/categories', [
    'name_en' => 'Restaurants',
    'name_ar' => 'المطاعم',
    'logo' => 'url',
    'is_active' => true,
    'sort_order' => 0
]);

// 4. Create subcategory
$response = Http::withToken($token)->post('http://localhost/api/v1/admin/subcategories', [
    'category_id' => 1,
    'name_en' => 'Italian',
    'name_ar' => 'إيطالي',
    'image' => 'url',
    'is_active' => true,
    'sort_order' => 0
]);

// 5. Create rating criteria with bilingual support
$response = Http::withToken($token)->post('http://localhost/api/v1/admin/rating-criteria', [
    'name_en' => 'Food Quality',
    'name_ar' => 'جودة الطعام',
    'type' => 'RATING',
    'subcategory_id' => 1,
    'is_required' => true,
    'is_active' => true,
    'sort_order' => 0
]);

// 6. Add choices to multiple choice criteria
$response = Http::withToken($token)->post(
    'http://localhost/api/v1/admin/rating-criteria/5/choices',
    [
        'name_en' => 'Excellent',
        'name_ar' => 'ممتاز',
        'value' => 5,
        'is_active' => true,
        'sort_order' => 0
    ]
);

// 7. View all rating criteria with choices
$response = Http::withToken($token)->get('http://localhost/api/v1/admin/rating-criteria');
// Returns criteria with bilingual questions and choices

// 8. Logout
$response = Http::withToken($token)->post('http://localhost/api/v1/admin/auth/logout');
```

### Example 2: Role and Permission Management

```php
// 1. Get all permissions
$response = Http::withToken($token)->get('http://localhost/api/v1/admin/permissions');

// 2. Create new role
$response = Http::withToken($token)->post('http://localhost/api/v1/admin/roles', [
    'name' => 'Content Editor',
    'guard' => 'admin',
    'description' => 'Can edit all content'
]);

$role_id = $response['data']['id'];

// 3. Assign permissions to role
$response = Http::withToken($token)->post(
    "http://localhost/api/v1/admin/roles/{$role_id}/sync-permissions",
    [
        'permission_ids' => [1, 2, 3, 4, 5] // Array of permission IDs
    ]
);

// 4. Assign role to admin
$admin = Admin::find(2);
$admin->roles()->sync([1]); // Assign role ID 1

// 5. Check permissions in code
$admin = Admin::with('roles.permissions')->find(2);
$permissions = $admin->permissions(); // Flattened permission collection
$canCreate = $permissions->where('name', 'catalog.categories.create')->exists();
```

### Example 3: Bilingual Rating Criteria Management

```php
// Create RATING type criteria
$criteria_response = Http::withToken($token)->post(
    'http://localhost/api/v1/admin/rating-criteria',
    [
        'name_en' => 'Service Quality',
        'name_ar' => 'جودة الخدمة',
        'type' => 'RATING',
        'subcategory_id' => 4,
        'is_required' => true,
        'is_active' => true,
        'sort_order' => 0
    ]
);

// Create YES_NO type criteria
$yesno_response = Http::withToken($token)->post(
    'http://localhost/api/v1/admin/rating-criteria',
    [
        'name_en' => 'Would you recommend?',
        'name_ar' => 'هل ستوصي بنا؟',
        'type' => 'YES_NO',
        'subcategory_id' => 4,
        'is_required' => false,
        'is_active' => true,
        'sort_order' => 1
    ]
);

// Create MULTIPLE_CHOICE type criteria
$choice_criteria_response = Http::withToken($token)->post(
    'http://localhost/api/v1/admin/rating-criteria',
    [
        'name_en' => 'Type of issue',
        'name_ar' => 'نوع المشكلة',
        'type' => 'MULTIPLE_CHOICE',
        'subcategory_id' => 4,
        'is_required' => false,
        'is_active' => true,
        'sort_order' => 2
    ]
);

$criteria_id = $choice_criteria_response['data']['id'];

// Add choices with bilingual labels
$choices_data = [
    ['name_en' => 'Poor Quality', 'name_ar' => 'جودة سيئة', 'value' => 1],
    ['name_en' => 'Rude Staff', 'name_ar' => 'موظفون وقحون', 'value' => 2],
    ['name_en' => 'Dirty Facility', 'name_ar' => 'مرفق قذر', 'value' => 3],
    ['name_en' => 'High Price', 'name_ar' => 'سعر مرتفع', 'value' => 4],
];

foreach ($choices_data as $choice) {
    Http::withToken($token)->post(
        "http://localhost/api/v1/admin/rating-criteria/{$criteria_id}/choices",
        $choice
    );
}

// Retrieve all criteria with choices
$all_criteria = Http::withToken($token)->get(
    'http://localhost/api/v1/admin/rating-criteria'
);

// Response includes:
// [
//   {
//     "id": 1,
//     "name_en": "Service Quality",
//     "name_ar": "جودة الخدمة",
//     "type": "RATING",
//     "choices": [],
//     "timestamps": {...}
//   },
//   {
//     "id": 3,
//     "name_en": "Type of issue",
//     "name_ar": "نوع المشكلة",
//     "type": "MULTIPLE_CHOICE",
//     "choices": [
//       {
//         "id": 1,
//         "name_en": "Poor Quality",
//         "name_ar": "جودة سيئة",
//         "value": 1
//       },
//       ...
//     ],
//     "timestamps": {...}
//   }
// ]
```

---

## Security Considerations

### Password Security
- Passwords are hashed using Laravel's `Hash` facade
- Uses bcrypt by default
- Never log or expose password hashes

### Token Security
- Sanctum tokens stored in `personal_access_tokens` table
- Tokens can be revoked on logout
- Set token expiration in `config/sanctum.php`

### Permission Security
- Always check permissions before returning sensitive data
- Use middleware to protect routes
- Flatten permissions from roles to prevent role-based bypasses
- Never trust client-side permission claims

### Data Protection
- Soft deletes preserve admin history
- Sensitive fields hidden from API responses
- Sanitize all user inputs in forms

---

## Troubleshooting

### Common Issues

**1. Login Returns 403 Forbidden**
- Check if admin `is_active` is true
- Verify admin has at least one role assigned
- Check role has required permissions

**2. Cannot Access Protected Routes**
- Verify token is valid and not expired
- Check Authorization header format: `Bearer {token}`
- Verify admin has required permission for route

**3. Rating Criteria not showing choices**
- Ensure criteria type is `MULTIPLE_CHOICE`
- Check choices are active (`is_active = true`)
- Verify choices are associated with criteria

**4. Bilingual content not showing**
- Ensure both `question_en`/`question_ar` or `choice_en`/`choice_ar` are populated
- Check resource mapping is correct
- Verify JSON response includes both language fields

---

## Related Documentation

- [Database Migration Files](../migrations/)
- [Model Relationships](../Models/)
- [Controller Implementation](../Controllers/)
- [Service Layer Logic](../Services/)
- [Resource Formatting](../Resources/)
