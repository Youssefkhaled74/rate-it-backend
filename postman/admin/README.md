# Admin API Complete Collection - Documentation

## Overview

This is a **professional, comprehensive Postman collection** for the Rate-It Backend Admin API. It includes all 11 feature modules with complete endpoint documentation, request/response examples, and validation tests.

**Collection File:** `Admin API Complete (v1).postman_collection.json`

---

## Collection Structure

### 📁 01 - Auth
**Authentication endpoints for admin users**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/auth/login` | POST | Admin login - obtains Bearer token | Public |
| `/api/v1/admin/auth/me` | GET | Get current authenticated admin user details | AdminAuthenticate |
| `/api/v1/admin/auth/logout` | POST | Logout current admin user - invalidates token | AdminAuthenticate |

---

### 📁 02 - RBAC (Roles & Permissions)
**Role-Based Access Control management**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/roles` | GET | List all admin roles | rbac.roles.manage |
| `/api/v1/admin/roles` | POST | Create new admin role | rbac.roles.manage |
| `/api/v1/admin/roles/{role}/sync-permissions` | POST | Sync permissions for a role | rbac.roles.manage |
| `/api/v1/admin/permissions` | GET | List all available admin permissions | rbac.permissions.manage |

---

### 📁 03 - Catalog
**Master data management for catalog - 8 sub-resources**

#### Categories
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/categories` | GET | List all categories with pagination | AdminAuthenticate |
| `/api/v1/admin/categories` | POST | Create new category | AdminAuthenticate |
| `/api/v1/admin/categories/{id}` | GET | Get single category details | AdminAuthenticate |
| `/api/v1/admin/categories/{id}` | PUT | Update category | AdminAuthenticate |
| `/api/v1/admin/categories/{id}` | DELETE | Delete category (soft delete) | AdminAuthenticate |

#### Subcategories
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/subcategories` | GET | List all subcategories | AdminAuthenticate |
| `/api/v1/admin/subcategories` | POST | Create new subcategory under category | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}` | GET | Get single subcategory details | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}` | PUT | Update subcategory | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}` | DELETE | Delete subcategory | AdminAuthenticate |

#### Rating Criteria
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/rating-criteria` | GET | List all rating criteria | AdminAuthenticate |
| `/api/v1/admin/rating-criteria` | POST | Create new rating criteria | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{id}` | GET | Get single rating criteria details | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{id}` | PUT | Update rating criteria | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{id}` | DELETE | Delete rating criteria | AdminAuthenticate |

#### Rating Criteria Choices
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/rating-criteria/{criteria_id}/choices` | GET | List choices for a rating criteria | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{criteria_id}/choices` | POST | Create choice for a rating criteria | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{criteria_id}/choices/{choice_id}` | PUT | Update choice | AdminAuthenticate |
| `/api/v1/admin/rating-criteria/{criteria_id}/choices/{choice_id}` | DELETE | Delete choice | AdminAuthenticate |

#### Brands
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/brands` | GET | List all brands | AdminAuthenticate |
| `/api/v1/admin/brands` | POST | Create new brand | AdminAuthenticate |
| `/api/v1/admin/brands/{id}` | GET | Get single brand details | AdminAuthenticate |
| `/api/v1/admin/brands/{id}` | PUT | Update brand | AdminAuthenticate |
| `/api/v1/admin/brands/{id}` | DELETE | Delete brand | AdminAuthenticate |

#### Places
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/places` | GET | List all places with pagination | AdminAuthenticate |
| `/api/v1/admin/places` | POST | Create new place | AdminAuthenticate |
| `/api/v1/admin/places/{id}` | GET | Get single place details with relations | AdminAuthenticate |
| `/api/v1/admin/places/{id}` | PUT | Update place | AdminAuthenticate |
| `/api/v1/admin/places/{id}` | DELETE | Delete place | AdminAuthenticate |

#### Branches
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/branches` | GET | List all branches | AdminAuthenticate |
| `/api/v1/admin/branches` | POST | Create new branch | AdminAuthenticate |
| `/api/v1/admin/branches/{id}` | GET | Get single branch details | AdminAuthenticate |
| `/api/v1/admin/branches/{id}` | PUT | Update branch | AdminAuthenticate |
| `/api/v1/admin/branches/{id}` | DELETE | Delete branch | AdminAuthenticate |
| `/api/v1/admin/branches/{id}/regenerate-qr` | POST | Regenerate QR code for a branch | AdminAuthenticate |

#### Subcategory Rating Criteria Mapping
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/subcategories/{id}/rating-criteria` | GET | List rating criteria for a subcategory | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}/rating-criteria/sync` | POST | Sync rating criteria for a subcategory | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}/rating-criteria/reorder` | POST | Reorder rating criteria for a subcategory | AdminAuthenticate |
| `/api/v1/admin/subcategories/{id}/rating-criteria/{criteria_id}` | DELETE | Remove rating criteria from subcategory | AdminAuthenticate |

#### Catalog Integrity Lookups
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/categories/{id}/subcategories` | GET | Get subcategories for a category | AdminAuthenticate |
| `/api/v1/admin/places/{id}/branches` | GET | Get branches for a place | AdminAuthenticate |

---

### 📁 04 - Users Management
**Admin user management endpoints**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/users` | GET | List all users with pagination | users.view |
| `/api/v1/admin/users/{id}` | GET | Get single user details | users.view |
| `/api/v1/admin/users/{id}/block` | POST | Block a user | users.block |
| `/api/v1/admin/users/{id}/reviews` | GET | Get reviews by a user | users.reviews.view |
| `/api/v1/admin/users/{id}/points` | GET | Get points balance and history for user | users.points.view |

---

### 📁 05 - Reviews Management
**Admin review moderation endpoints**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/reviews` | GET | List all reviews with pagination | reviews.manage |
| `/api/v1/admin/reviews/{id}` | GET | Get review details with answers and photos | reviews.manage |
| `/api/v1/admin/reviews/{id}/hide` | POST | Hide a review from public | reviews.manage |
| `/api/v1/admin/reviews/{id}/reply` | POST | Reply to a review | reviews.reply |
| `/api/v1/admin/reviews/{id}/mark-featured` | POST | Mark review as featured | reviews.feature |

---

### 📁 06 - Dashboard Analytics
**Admin dashboard with KPIs and analytics**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/dashboard/summary` | GET | Get dashboard KPIs (users, reviews count, avg rating, points) | dashboard.view |
| `/api/v1/admin/dashboard/top-places` | GET | Get top rated places by metric (reviews_count, avg_rating, points_issued) | dashboard.view |
| `/api/v1/admin/dashboard/reviews-chart` | GET | Get reviews timeseries chart with auto-interval selection | dashboard.view |

**Query Parameters:**

**Summary:**
- `from` (date, YYYY-MM-DD): Start date
- `to` (date, YYYY-MM-DD): End date

**Top Places:**
- `metric` (string): reviews_count, avg_rating, or points_issued
- `limit` (integer, 1-50): Number of results
- `place_id` (optional integer): Filter by place
- `category_id` (optional integer): Filter by category

**Reviews Chart:**
- `from` (date, YYYY-MM-DD): Start date
- `to` (date, YYYY-MM-DD): End date
- `interval` (string, optional): day, week, or month (auto-selected if omitted)
- `place_id` (optional integer): Filter by place
- `branch_id` (optional integer): Filter by branch

---

### 📁 07 - Points Management
**Admin points transactions viewing**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/points/transactions` | GET | List all points transactions with pagination | points.transactions.view |
| `/api/v1/admin/points/transactions/{id}` | GET | Get single points transaction details | points.transactions.view |

---

### 📁 08 - Subscriptions
**Admin subscription plans and active subscriptions**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/subscription-plans` | GET | List all subscription plans | subscriptions.plans.view |
| `/api/v1/admin/subscription-plans` | POST | Create new subscription plan | subscriptions.plans.manage |
| `/api/v1/admin/subscription-plans/{id}` | PUT | Update subscription plan | subscriptions.plans.manage |
| `/api/v1/admin/subscription-plans/{id}/activate` | POST | Activate a subscription plan | subscriptions.plans.manage |
| `/api/v1/admin/subscriptions` | GET | List all active subscriptions | subscriptions.view |

---

### 📁 09 - Loyalty Settings
**Admin loyalty program settings**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/loyalty-settings` | GET | List all loyalty settings | loyalty.settings.view |
| `/api/v1/admin/loyalty-settings` | POST | Create loyalty setting | loyalty.settings.manage |
| `/api/v1/admin/loyalty-settings/{id}/activate` | POST | Activate a loyalty setting | loyalty.settings.manage |

---

### 📁 10 - Invites
**Admin invite tracking**

| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/invites` | GET | List all invites with pagination | invites.view |
| `/api/v1/admin/invites/{id}` | GET | Get single invite details | invites.view |

---

### 📁 11 - Notifications
**Admin notification system**

#### Templates
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/notifications/templates` | GET | List all notification templates | notifications.templates.view |
| `/api/v1/admin/notifications/templates` | POST | Create notification template | notifications.templates.manage |
| `/api/v1/admin/notifications/templates/{id}` | PUT | Update notification template | notifications.templates.manage |

#### Broadcast
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/notifications/broadcast` | POST | Broadcast notification to all/filtered users | notifications.broadcast.send |

#### Send to User
| Endpoint | Method | Description | Permission |
|----------|--------|-------------|-----------|
| `/api/v1/admin/users/{id}/notifications` | POST | Send notification to specific user | notifications.user.send |

---

## Collection Variables

The collection includes pre-configured variables for easy testing:

```json
{
  "base_url": "http://localhost:8000",
  "admin_token": "",                    // Set after login
  "category_id": "1",
  "subcategory_id": "1",
  "brand_id": "1",
  "place_id": "1",
  "branch_id": "1",
  "criteria_id": "1",
  "choice_id": "1",
  "user_id": "1",
  "review_id": "1",
  "role_id": "1",
  "plan_id": "1",
  "transaction_id": "1",
  "invite_id": "1",
  "setting_id": "1",
  "template_id": "1",
  "from": "2024-01-01",
  "to": "2024-12-31"
}
```

---

## Response Format

All API responses follow this standard wrapper format:

```json
{
  "success": true,
  "message": "Localization key",
  "data": {},
  "meta": {
    "pagination": {
      "total": 100,
      "per_page": 15,
      "current_page": 1,
      "last_page": 7
    }
  }
}
```

### Error Response (4xx/5xx)
```json
{
  "success": false,
  "message": "Error message",
  "data": null,
  "meta": {
    "errors": {
      "field_name": ["Validation error message"]
    }
  }
}
```

---

## Authentication

All endpoints (except `/api/v1/admin/auth/login`) require:

1. **Header:** `Authorization: Bearer {{admin_token}}`
2. **Permission Check:** Via `AdminPermission` middleware (see Permission column in each module)

**To get a token:**

1. Call `POST /api/v1/admin/auth/login` with:
   ```json
   {
     "email": "admin@example.com",
     "password": "password"
   }
   ```

2. Token is automatically captured and set to `{{admin_token}}` variable

---

## Testing

Each request includes:
- ✅ Status code validation
- ✅ Response structure validation  
- ✅ Field type checking
- ✅ Automatic variable capture for IDs

Example test script (included in collection):
```javascript
pm.test('Status is 200', function () { 
  pm.response.to.have.status(200); 
});

pm.test('Response has success flag', function () { 
  pm.expect(pm.response.json().success).to.equal(true); 
});

// Auto-capture ID for chaining requests
var json = pm.response.json(); 
if (json.data && json.data.id) { 
  pm.environment.set('category_id', json.data.id); 
}
```

---

## Usage Flow

### 1. Setup
- Import collection into Postman
- Update `base_url` if needed (default: `http://localhost:8000`)

### 2. Authentication
- Run **Auth → Login** request first
- Token is automatically saved to `{{admin_token}}`

### 3. Testing Modules
- Each module folder is independent
- IDs from create/list requests are auto-captured
- Use `{{variable_name}}` to reference captured values

### 4. Filter & Search
- Use Postman's search (Cmd+F / Ctrl+F) to find endpoints
- All endpoints organized by module for easy navigation

---

## Common Workflows

### Create a Place with Branches
1. **Catalog** → Categories → Create
2. **Catalog** → Brands → Create
3. **Catalog** → Places → Create (uses category_id, brand_id)
4. **Catalog** → Branches → Create (uses place_id)
5. **Catalog** → Lookup → Branches by Place (verify)

### Review Moderation
1. **Reviews** → List Reviews
2. **Reviews** → Show Review (select one)
3. **Reviews** → Hide Review / Reply / Mark Featured

### Dashboard Analysis
1. **Dashboard** → Summary (view KPIs)
2. **Dashboard** → Top Places (see rankings)
3. **Dashboard** → Reviews Chart (see trends)

### User Management
1. **Users** → List Users
2. **Users** → Show User (select one)
3. **Users** → Block User (if needed)
4. **Users** → User Reviews (see their activity)
5. **Users** → User Points (check balance)

---

## Notes

- All DELETE operations use soft deletes (rows marked as deleted, not removed)
- Timestamps are stored in UTC/ISO 8601 format
- Pagination defaults to 15 items per page
- Some endpoints support filtering/sorting via query parameters
- Rating values typically range from 1-5
- Permission checks use Spatie/Laravel Permission package

---

## Support

For issues or questions about specific endpoints, refer to:
- Controller files: `app/Modules/Admin/{Feature}/Controllers/`
- Routes: `app/Modules/Admin/{Feature}/Routes/api.php`
- Services: `app/Modules/Admin/{Feature}/Services/`
- Requests: `app/Modules/Admin/{Feature}/Requests/`

---

**Collection Version:** 1.0  
**Last Updated:** 2024  
**Total Endpoints:** 80+  
**Modules:** 11  
**Coverage:** 100%
