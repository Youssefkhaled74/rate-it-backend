# Admin Module - Quick Reference Guide

## Quick Links

- [Full Documentation](./ADMIN_DOCUMENTATION.md)
- [Database Schema](#database-schema)
- [API Quick Reference](#api-quick-reference)
- [Permission Checklist](#permission-checklist)

---

## API Quick Reference

### Auth
| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | `/auth/login` | No | Admin login |
| POST | `/auth/logout` | Yes | Admin logout |
| GET | `/auth/me` | Yes | Get current admin |

### Dashboard
| Method | Endpoint | Auth | Permission |
|--------|----------|------|-----------|
| GET | `/dashboard/summary` | Yes | `dashboard.view` |
| GET | `/dashboard/top-places` | Yes | `dashboard.view` |
| GET | `/dashboard/reviews-chart` | Yes | `dashboard.view` |

### Catalog - Categories
| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| GET | `/categories` | Yes | List categories |
| POST | `/categories` | Yes | Create category |
| GET | `/categories/{id}` | Yes | Show category |
| PUT | `/categories/{id}` | Yes | Update category |
| DELETE | `/categories/{id}` | Yes | Delete category |

### Catalog - Subcategories
| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| GET | `/subcategories` | Yes | List subcategories |
| POST | `/subcategories` | Yes | Create subcategory |
| GET | `/subcategories/{id}` | Yes | Show subcategory |
| PUT | `/subcategories/{id}` | Yes | Update subcategory |
| DELETE | `/subcategories/{id}` | Yes | Delete subcategory |

### Catalog - Rating Criteria
| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| GET | `/rating-criteria` | Yes | List criteria |
| POST | `/rating-criteria` | Yes | Create criteria |
| GET | `/rating-criteria/{id}` | Yes | Show criteria |
| PUT | `/rating-criteria/{id}` | Yes | Update criteria |
| DELETE | `/rating-criteria/{id}` | Yes | Delete criteria |
| GET | `/rating-criteria/{id}/choices` | Yes | List choices |
| POST | `/rating-criteria/{id}/choices` | Yes | Create choice |
| PUT | `/rating-criteria/{id}/choices/{choice_id}` | Yes | Update choice |
| DELETE | `/rating-criteria/{id}/choices/{choice_id}` | Yes | Delete choice |

### RBAC
| Method | Endpoint | Auth | Permission |
|--------|----------|------|-----------|
| GET | `/roles` | Yes | `rbac.roles.manage` |
| POST | `/roles` | Yes | `rbac.roles.manage` |
| POST | `/roles/{id}/sync-permissions` | Yes | `rbac.roles.manage` |
| GET | `/permissions` | Yes | `rbac.permissions.manage` |

---

## Request/Response Formats

### Login Request
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

### Category Request
```json
{
    "name_en": "Restaurants",
    "name_ar": "المطاعم",
    "logo": "https://...",
    "is_active": true,
    "sort_order": 0
}
```

### Rating Criteria Request
```json
{
    "name_en": "Cleanliness",
    "name_ar": "النظافة",
    "type": "RATING|YES_NO|MULTIPLE_CHOICE",
    "subcategory_id": 4,
    "is_required": true,
    "is_active": true,
    "sort_order": 0
}
```

### Rating Choice Request
```json
{
    "name_en": "Excellent",
    "name_ar": "ممتاز",
    "value": 5,
    "is_active": true,
    "sort_order": 0
}
```

### Role Create Request
```json
{
    "name": "Content Manager",
    "guard": "admin",
    "description": "Manages catalog content"
}
```

### Sync Permissions Request
```json
{
    "permission_ids": [1, 2, 3, 4, 5]
}
```

---

## Database Schema Summary

### Core Tables
- **admins**: Admin user accounts
- **roles**: Role definitions
- **permissions**: Permission definitions
- **model_has_roles**: Admin-Role relationships (pivot)
- **role_has_permissions**: Role-Permission relationships (pivot)

### Catalog Tables
- **categories**: Product/service categories
- **subcategories**: Subcategories under categories
- **brands**: Brand information
- **places**: Business locations/venues
- **branches**: Branches of places
- **rating_criteria**: Review questions/criteria
- **rating_criteria_choices**: Options for multiple-choice criteria

---

## Permission Checklist

### Admin Permissions
- `admin.auth.login` - Login access
- `admin.users.view` - View admins
- `admin.users.create` - Create admins
- `admin.users.update` - Update admins
- `admin.users.delete` - Delete admins

### Catalog Permissions
- `catalog.categories.view`
- `catalog.categories.create`
- `catalog.categories.update`
- `catalog.categories.delete`
- `catalog.subcategories.view`
- `catalog.subcategories.create`
- `catalog.subcategories.update`
- `catalog.subcategories.delete`
- `catalog.brands.view`
- `catalog.brands.create`
- `catalog.brands.update`
- `catalog.brands.delete`
- `catalog.places.view`
- `catalog.places.create`
- `catalog.places.update`
- `catalog.places.delete`
- `catalog.branches.view`
- `catalog.branches.create`
- `catalog.branches.update`
- `catalog.branches.delete`
- `catalog.rating-criteria.view`
- `catalog.rating-criteria.create`
- `catalog.rating-criteria.update`
- `catalog.rating-criteria.delete`
- `catalog.rating-criteria-choices.manage`

### Review Permissions
- `reviews.view` - View reviews
- `reviews.approve` - Approve reviews
- `reviews.reject` - Reject reviews
- `reviews.delete` - Delete reviews

### Dashboard Permissions
- `dashboard.view` - View dashboard

### RBAC Permissions
- `rbac.roles.manage` - Manage roles
- `rbac.permissions.manage` - Manage permissions

---

## Admin Workflow States

```
┌─────────────────────────────┐
│  ADMIN CREATED              │
│  - Record inserted          │
│  - Password hashed          │
│  - is_active = true         │
└────────────┬────────────────┘
             │
             ▼
┌─────────────────────────────┐
│  ROLE ASSIGNMENT            │
│  - Assign to 1+ roles       │
│  - Inherit permissions      │
└────────────┬────────────────┘
             │
             ▼
┌─────────────────────────────┐
│  LOGIN                      │
│  - POST /auth/login         │
│  - Verify password          │
│  - Generate Sanctum token   │
│  - Return admin with roles  │
└────────────┬────────────────┘
             │
             ▼
┌─────────────────────────────┐
│  API ACCESS                 │
│  - Send Authorization token │
│  - Check permissions        │
│  - Execute operation        │
└────────────┬────────────────┘
             │
             ▼
┌─────────────────────────────┐
│  LOGOUT                     │
│  - POST /auth/logout        │
│  - Revoke token             │
└────────────┬────────────────┘
             │
             ▼
┌─────────────────────────────┐
│  DEACTIVATION (optional)    │
│  - Set is_active = false    │
│  - Cannot login             │
│  - Data preserved           │
└─────────────────────────────┘
```

---

## Common Curl Examples

### Login
```bash
curl -X POST http://localhost/api/v1/admin/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

### List Categories
```bash
curl -X GET http://localhost/api/v1/admin/categories \
  -H "Authorization: Bearer {TOKEN}"
```

### Create Category
```bash
curl -X POST http://localhost/api/v1/admin/categories \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name_en": "Restaurants",
    "name_ar": "المطاعم",
    "logo": "https://...",
    "is_active": true,
    "sort_order": 0
  }'
```

### Create Rating Criteria
```bash
curl -X POST http://localhost/api/v1/admin/rating-criteria \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name_en": "Cleanliness",
    "name_ar": "النظافة",
    "type": "RATING",
    "subcategory_id": 4,
    "is_required": true,
    "is_active": true,
    "sort_order": 0
  }'
```

### Add Choice to Criteria
```bash
curl -X POST http://localhost/api/v1/admin/rating-criteria/1/choices \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name_en": "Excellent",
    "name_ar": "ممتاز",
    "value": 5,
    "is_active": true,
    "sort_order": 0
  }'
```

### Logout
```bash
curl -X POST http://localhost/api/v1/admin/auth/logout \
  -H "Authorization: Bearer {TOKEN}"
```

---

## Environment Configuration

### Key Config Files
- `config/auth.php` - Authentication configuration
- `config/sanctum.php` - Token settings
- `.env` - Database and environment variables

### Important Settings
```env
DB_HOST=127.0.0.1
DB_DATABASE=rate-it-database
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SANCTUM_GUARD=admin
```

---

## Troubleshooting Guide

| Problem | Solution |
|---------|----------|
| 401 Unauthorized | Check token in Authorization header |
| 403 Forbidden | Verify admin has required permission |
| Category not found | Check category ID exists and is not deleted |
| Criteria choices empty | Ensure type is MULTIPLE_CHOICE and choices are active |
| Password mismatch on login | Verify password is correct |
| Token expired | Request new token by logging in again |

---

## Related Resources

- [Full Admin Documentation](./ADMIN_DOCUMENTATION.md)
- [Database Migrations](../database/migrations/)
- [Admin Models](../app/Models/)
- [Admin Module Controllers](../app/Modules/Admin/)
