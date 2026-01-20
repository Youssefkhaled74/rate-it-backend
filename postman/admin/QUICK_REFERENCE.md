# Admin API - Quick Reference

## 🚀 Getting Started (2 Minutes)

### 1. Login
```bash
POST /api/v1/admin/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```
→ Save `data.token` as `{{admin_token}}`

### 2. All Subsequent Requests
```bash
Authorization: Bearer {{admin_token}}
```

---

## 📊 Module Quick Stats

| Module | Endpoints | Key Permissions | Status |
|--------|-----------|-----------------|--------|
| Auth | 3 | public, AdminAuthenticate | ✅ Complete |
| RBAC | 4 | rbac.roles.manage, rbac.permissions.manage | ✅ Complete |
| Catalog | 43 | AdminAuthenticate | ✅ Complete |
| Users | 5 | users.*, AdminAuthenticate | ✅ Complete |
| Reviews | 5 | reviews.* | ✅ Complete |
| Dashboard | 3 | dashboard.view | ✅ Complete |
| Points | 2 | points.transactions.view | ✅ Complete |
| Subscriptions | 5 | subscriptions.* | ✅ Complete |
| Loyalty | 3 | loyalty.settings.* | ✅ Complete |
| Invites | 2 | invites.view | ✅ Complete |
| Notifications | 5 | notifications.* | ✅ Complete |
| **TOTAL** | **80+** | | ✅ **All** |

---

## 🎯 Most Common Endpoints

```bash
# Authentication
POST   /api/v1/admin/auth/login
GET    /api/v1/admin/auth/me
POST   /api/v1/admin/auth/logout

# Dashboard
GET    /api/v1/admin/dashboard/summary
GET    /api/v1/admin/dashboard/top-places
GET    /api/v1/admin/dashboard/reviews-chart

# Master Data (CRUD Pattern)
GET    /api/v1/admin/categories
POST   /api/v1/admin/categories
GET    /api/v1/admin/categories/{id}
PUT    /api/v1/admin/categories/{id}
DELETE /api/v1/admin/categories/{id}
# ↑ Repeat for: subcategories, brands, places, branches, rating-criteria

# Users
GET    /api/v1/admin/users
GET    /api/v1/admin/users/{id}
POST   /api/v1/admin/users/{id}/block
GET    /api/v1/admin/users/{id}/reviews
GET    /api/v1/admin/users/{id}/points

# Reviews
GET    /api/v1/admin/reviews
GET    /api/v1/admin/reviews/{id}
POST   /api/v1/admin/reviews/{id}/hide
POST   /api/v1/admin/reviews/{id}/reply
POST   /api/v1/admin/reviews/{id}/mark-featured
```

---

## 🔐 Permission Checklist

### Core Permissions
- `rbac.roles.manage` - Create/manage roles
- `rbac.permissions.manage` - View permissions
- `users.view` - View user list
- `users.block` - Block users
- `reviews.manage` - List/view/hide reviews
- `reviews.reply` - Reply to reviews
- `reviews.feature` - Mark reviews as featured
- `dashboard.view` - Access dashboard analytics

### Extended Permissions
- `users.reviews.view` - View user's reviews
- `users.points.view` - View user's points
- `points.transactions.view` - View points transactions
- `subscriptions.plans.view` - View subscription plans
- `subscriptions.plans.manage` - Manage subscription plans
- `subscriptions.view` - View active subscriptions
- `loyalty.settings.view` - View loyalty settings
- `loyalty.settings.manage` - Manage loyalty settings
- `invites.view` - View invites
- `notifications.templates.view` - View notification templates
- `notifications.templates.manage` - Manage notification templates
- `notifications.broadcast.send` - Send broadcast notifications
- `notifications.user.send` - Send user notifications

---

## 📋 Catalog Management Workflow

### Create Place with Full Setup
```bash
# 1. Create Category
POST /api/v1/admin/categories
{ "name": "Restaurants", "description": "..." }
→ save category_id

# 2. Create Brand
POST /api/v1/admin/brands
{ "name": "McDonald's", "description": "..." }
→ save brand_id

# 3. Create Place
POST /api/v1/admin/places
{ "name": "Downtown", "category_id": 1, "brand_id": 1, ... }
→ save place_id

# 4. Create Branches
POST /api/v1/admin/branches
{ "place_id": 1, "name": "Main Branch", ... }
→ save branch_id

# 5. Create Rating Criteria
POST /api/v1/admin/rating-criteria
{ "name": "Cleanliness", "type": "rating" }
→ save criteria_id

# 6. Create Choices for Criteria
POST /api/v1/admin/rating-criteria/1/choices
{ "value": "Very Clean", "order": 1 }

# 7. Link Criteria to Subcategory
POST /api/v1/admin/subcategories/{id}/rating-criteria/sync
{ "criteria_ids": [1, 2, 3] }
```

---

## 📈 Dashboard Query Examples

```bash
# Summary - All time
GET /api/v1/admin/dashboard/summary

# Summary - Date range
GET /api/v1/admin/dashboard/summary?from=2024-01-01&to=2024-12-31

# Top Places - By reviews count
GET /api/v1/admin/dashboard/top-places?metric=reviews_count&limit=10

# Top Places - By average rating
GET /api/v1/admin/dashboard/top-places?metric=avg_rating&limit=5

# Top Places - By points issued
GET /api/v1/admin/dashboard/top-places?metric=points_issued&limit=20

# Reviews Chart - Auto interval
GET /api/v1/admin/dashboard/reviews-chart?from=2024-01-01&to=2024-12-31

# Reviews Chart - Weekly data
GET /api/v1/admin/dashboard/reviews-chart?from=2024-01-01&to=2024-12-31&interval=week

# Reviews Chart - Daily data for specific place
GET /api/v1/admin/dashboard/reviews-chart?from=2024-01-01&to=2024-12-31&interval=day&place_id=1
```

---

## ✅ Response Examples

### Success (200, 201)
```json
{
  "success": true,
  "message": "dashboard.summary",
  "data": {
    "users_count": 1500,
    "reviews_count": 3200,
    "avg_rating": 4.5,
    "points_issued_total": 50000,
    "points_redeemed_total": 12000
  },
  "meta": null
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "data": null,
  "meta": {
    "errors": {
      "email": ["The email field is required"],
      "name": ["The name field must be at least 3 characters"]
    }
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found",
  "data": null,
  "meta": null
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized",
  "data": null,
  "meta": null
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "You don't have permission to perform this action",
  "data": null,
  "meta": null
}
```

---

## 🔄 Variable Reference

```json
{
  "{{base_url}}": "http://localhost:8000",
  "{{admin_token}}": "eyJhbGc...",
  
  "{{category_id}}": "1",
  "{{subcategory_id}}": "1",
  "{{brand_id}}": "1",
  "{{place_id}}": "1",
  "{{branch_id}}": "1",
  "{{criteria_id}}": "1",
  "{{choice_id}}": "1",
  "{{user_id}}": "1",
  "{{review_id}}": "1",
  "{{role_id}}": "1",
  "{{plan_id}}": "1",
  "{{transaction_id}}": "1",
  "{{invite_id}}": "1",
  "{{setting_id}}": "1",
  "{{template_id}}": "1",
  
  "{{from}}": "2024-01-01",
  "{{to}}": "2024-12-31"
}
```

---

## 🐛 Common Issues & Solutions

### Issue: 401 Unauthorized
**Cause:** Missing or invalid token
**Solution:** Run Auth → Login first, token auto-captured to `{{admin_token}}`

### Issue: 403 Forbidden
**Cause:** Missing required permission
**Solution:** Check permission in column 3 of module table, sync role permissions

### Issue: 404 Not Found
**Cause:** Invalid ID (wrong variable or deleted resource)
**Solution:** Run List endpoint first, capture correct ID from response

### Issue: 422 Validation Error
**Cause:** Missing/invalid required fields
**Solution:** Check request body in example, read `meta.errors` response

### Issue: Empty {{variable_name}}
**Cause:** Dependent request not run first
**Solution:** Always create before detail/update/delete
- Create (POST) → ID auto-captured
- Then use Show (GET) / Update (PUT) / Delete (DELETE)

---

## 📚 Module Load Order

**Recommended sequence for initial setup:**

1. **Auth** - Login first
2. **RBAC** - Setup roles and permissions
3. **Catalog** - Create master data (categories, brands, places, branches)
4. **Loyalty Settings** - Configure points rules
5. **Subscriptions** - Create subscription plans
6. **Notifications** - Setup notification templates
7. **Users** - Manage admin users
8. **Dashboard** - View analytics
9. **Reviews** - Moderate content
10. **Points** - Monitor transactions
11. **Invites** - Track invitations

---

## 📞 API Design Notes

- **Soft Deletes:** All DELETE operations are soft deletes (deleted_at field)
- **Pagination:** 15 items per page by default, supports `?page=N&per_page=M`
- **Timestamps:** All dates in UTC ISO 8601 format
- **IDs:** Integer primary keys, use in URL path
- **Filtering:** Most LIST endpoints support filters via query strings
- **Sorting:** Supports `?sort=field` and `?sort=-field` (descending)
- **Search:** Some LIST endpoints support `?search=term`

---

## 🎓 Learn More

Full documentation: See `README.md` in this folder
- Detailed endpoint descriptions
- All query parameters explained
- Complete response structure examples
- Workflow examples for each module

Code references:
- Controllers: `app/Modules/Admin/*/Controllers/`
- Routes: `app/Modules/Admin/*/Routes/api.php`
- Services: `app/Modules/Admin/*/Services/`
- Requests: `app/Modules/Admin/*/Requests/`
- Models: `app/Models/`

---

**Last Updated:** 2024  
**Collection Version:** 1.0  
**Status:** Production Ready ✅
