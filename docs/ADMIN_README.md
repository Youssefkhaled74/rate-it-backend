# Admin Module Documentation Index

## 📚 Complete Documentation Set

Welcome to the Rate-It Admin Module documentation. This comprehensive guide covers everything you need to know about the admin system.

---

## 📋 Documentation Files

### 1. **[Full Admin Documentation](./ADMIN_DOCUMENTATION.md)** ⭐ START HERE
Complete reference guide covering:
- Admin Architecture & Models
- Authentication & Authorization System
- Admin Lifecycle & Workflow
- All API Endpoints with examples
- Admin Roles & Permissions
- Database Schema
- Detailed Usage Examples
- Security Considerations
- Troubleshooting

**When to use**: Need comprehensive information about any aspect of the admin system

---

### 2. **[Quick Reference Guide](./ADMIN_QUICK_REFERENCE.md)** 🚀 QUICK LOOKUP
Quick lookup tables and checklists:
- API Endpoints Summary (one-page table)
- Request/Response Formats
- Permission Checklist
- Curl Examples
- Common Troubleshooting
- Environment Configuration

**When to use**: Need to quickly find an endpoint, permission, or curl example

---

### 3. **[Workflow & Flow Diagrams](./ADMIN_WORKFLOW_FLOWS.md)** 📊 VISUAL GUIDE
Detailed flow diagrams:
- Complete Admin Request Flow
- Authentication Flow (Login)
- Permission Check Flow
- Catalog Management Workflow
- Data Model Relationships
- Response Format Standard
- Error Response Format

**When to use**: Need to understand how flows work, how to visualize requests, or understand data relationships

---

## 🎯 Quick Navigation

### By Use Case

#### "I need to login as admin"
1. Read: [Login Flow](./ADMIN_WORKFLOW_FLOWS.md#authentication-flow---login)
2. See: [Login Curl Example](./ADMIN_QUICK_REFERENCE.md#login)
3. Refer: [Login Endpoint](./ADMIN_DOCUMENTATION.md#1-login)

#### "I want to manage categories"
1. Read: [Catalog Management Workflow](./ADMIN_WORKFLOW_FLOWS.md#catalog-management-workflow)
2. See: [Categories Endpoints](./ADMIN_QUICK_REFERENCE.md#catalog---categories)
3. See: [Categories Examples](./ADMIN_DOCUMENTATION.md#categories)

#### "I need to create rating criteria with bilingual support"
1. Read: [Rating Criteria Endpoints](./ADMIN_DOCUMENTATION.md#rating-criteria-endpoints-protected)
2. See: [Bilingual Example](./ADMIN_DOCUMENTATION.md#example-3-bilingual-rating-criteria-management)
3. Refer: [Rating Criteria Table](./ADMIN_QUICK_REFERENCE.md#api-quick-reference)

#### "I need to understand permissions"
1. Read: [Admin Roles & Permissions](./ADMIN_DOCUMENTATION.md#admin-roles--permissions)
2. See: [Permission Checklist](./ADMIN_QUICK_REFERENCE.md#permission-checklist)
3. See: [Permission Check Flow](./ADMIN_WORKFLOW_FLOWS.md#permission-check-flow)

#### "I'm getting a 403 Forbidden error"
1. See: [Permission Check Flow](./ADMIN_WORKFLOW_FLOWS.md#permission-check-flow)
2. Check: [Permission Checklist](./ADMIN_QUICK_REFERENCE.md#permission-checklist)
3. Read: [Troubleshooting](./ADMIN_DOCUMENTATION.md#troubleshooting)
4. See: [Common Issues](./ADMIN_QUICK_REFERENCE.md#troubleshooting-guide)

#### "I need to integrate the API"
1. Read: [Complete Admin Request Flow](./ADMIN_WORKFLOW_FLOWS.md#complete-admin-request-flow)
2. See: [Usage Examples](./ADMIN_DOCUMENTATION.md#usage-examples)
3. Refer: [All API Endpoints](./ADMIN_DOCUMENTATION.md#api-endpoints)

---

## 📑 Documentation Structure

```
docs/
├── ADMIN_DOCUMENTATION.md          ← Full reference (comprehensive)
├── ADMIN_QUICK_REFERENCE.md        ← Quick lookup (tables & commands)
├── ADMIN_WORKFLOW_FLOWS.md         ← Visual guides & flows
└── README.md                       ← You are here
```

---

## 🔑 Key Concepts

### Authentication
- **Guard**: `admin` (Sanctum-based)
- **Middleware**: `AdminAuthenticate` checks token validity
- **Token Storage**: `personal_access_tokens` table
- **User Model**: `App\Models\Admin`

### Authorization
- **Middleware**: `AdminPermission` checks permissions
- **Structure**: Admin → Roles → Permissions (RBAC)
- **Resolution**: Permissions flattened from all roles
- **Models**: Role, Permission, model_has_roles, role_has_permissions

### Data Management
- **Categories** → **Subcategories** → **RatingCriteria** → **Choices**
- **Places** ← has **Branches**, belongs to **Subcategory** and **Brand**
- **Bilingual Support**: `_en` and `_ar` suffixes for text fields

---

## 🔄 Admin Lifecycle

1. **Admin Created**: Record inserted into `admins` table
2. **Role Assigned**: Admin assigned to one or more roles
3. **Permission Inherited**: Admin gets all permissions from assigned roles
4. **Login**: Admin provides credentials, receives token
5. **API Access**: Admin sends token in Authorization header
6. **Permission Check**: Each request validates permission
7. **Logout**: Admin token is revoked
8. **Deactivation** (optional): is_active set to false

---

## 🚀 Common Tasks

### Task: Create a New Admin User
```php
// In code or via UI
$admin = Admin::create([
    'name' => 'John Manager',
    'email' => 'john@example.com',
    'phone' => '+966501234567',
    'password_hash' => Hash::make('password123'),
    'is_active' => true,
]);

// Assign role
$admin->roles()->sync([2]); // Role ID 2 = Category Manager

// Admin can now login
```

### Task: Create a Category and Set Up Criteria
```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost/api/v1/admin/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }' | jq -r '.data.token')

# 2. Create category
curl -X POST http://localhost/api/v1/admin/categories \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name_en": "Restaurants",
    "name_ar": "المطاعم",
    "logo": "https://..."
  }'

# 3. Create subcategory
curl -X POST http://localhost/api/v1/admin/subcategories \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "name_en": "Italian",
    "name_ar": "إيطالي"
  }'

# 4. Create rating criteria
curl -X POST http://localhost/api/v1/admin/rating-criteria \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name_en": "Cleanliness",
    "name_ar": "النظافة",
    "type": "RATING",
    "subcategory_id": 1,
    "is_required": true,
    "is_active": true
  }'
```

---

## 📊 API Endpoints Summary

**60+ Endpoints** across these modules:
- Auth (3 endpoints)
- Dashboard (3 endpoints)
- Catalog (31 endpoints)
- Rating Criteria (8 endpoints)
- RBAC (4 endpoints)
- Users (5+ endpoints)
- Reviews (4+ endpoints)
- And more...

See [API Quick Reference](./ADMIN_QUICK_REFERENCE.md#api-quick-reference) for complete list.

---

## 🔐 Permissions System

### 40+ Permissions
Organized by module:
- `admin.*` - Admin user management
- `catalog.*` - Catalog management
- `reviews.*` - Review moderation
- `dashboard.*` - Analytics
- `rbac.*` - Role management

See [Permission Checklist](./ADMIN_QUICK_REFERENCE.md#permission-checklist) for complete list.

---

## 🛠️ Developer Resources

### File Locations
```
app/
├── Models/
│   ├── Admin.php
│   ├── Role.php
│   └── Permission.php
├── Modules/
│   └── Admin/
│       ├── Auth/
│       │   ├── Controllers/AuthController.php
│       │   ├── Requests/
│       │   ├── Resources/AdminResource.php
│       │   └── Routes/api.php
│       ├── Catalog/
│       │   ├── Controllers/ (7 controllers)
│       │   ├── Services/ (7 services)
│       │   ├── Resources/ (7 resources)
│       │   ├── Requests/ (12+ requests)
│       │   └── Routes/api.php
│       ├── Dashboard/
│       ├── Users/
│       ├── Reviews/
│       ├── RBAC/
│       └── ... (more modules)
├── Http/
│   └── Middleware/
│       ├── AdminAuthenticate.php
│       └── AdminPermission.php
│
database/
├── migrations/
│   ├── *_create_admins_table.php
│   ├── *_create_roles_table.php
│   ├── *_create_permissions_table.php
│   ├── *_create_model_has_roles_table.php
│   └── ... (catalog migrations)
└── seeders/
    ├── AdminSeeder.php
    ├── RoleSeeder.php
    ├── PermissionSeeder.php
    └── ... (catalog seeders)

config/
├── auth.php (guard: admin)
└── sanctum.php (token settings)
```

### Related Documentation
- See codebase for implementation details
- Check migrations for schema definitions
- Review services for business logic
- Examine resources for output formatting

---

## 🎓 Learning Path

### Beginner
1. Read: [Full Documentation Overview](./ADMIN_DOCUMENTATION.md#overview)
2. Watch: [Authentication Flow](./ADMIN_WORKFLOW_FLOWS.md#authentication-flow---login)
3. Try: [Login Curl Example](./ADMIN_QUICK_REFERENCE.md#login)

### Intermediate
1. Understand: [Admin Lifecycle](./ADMIN_DOCUMENTATION.md#admin-lifecycle--workflow)
2. Learn: [Catalog Workflow](./ADMIN_WORKFLOW_FLOWS.md#catalog-management-workflow)
3. Practice: [Create Categories & Criteria](./ADMIN_DOCUMENTATION.md#usage-examples)

### Advanced
1. Master: [Permission System](./ADMIN_DOCUMENTATION.md#admin-roles--permissions)
2. Understand: [Database Schema](./ADMIN_DOCUMENTATION.md#database-schema)
3. Study: [Request/Response Flow](./ADMIN_WORKFLOW_FLOWS.md#complete-admin-request-flow)

---

## 📞 Support & Troubleshooting

### Common Issues
See [Troubleshooting Guide](./ADMIN_QUICK_REFERENCE.md#troubleshooting-guide)

### Error Codes
- `401` - Unauthorized (invalid token)
- `403` - Forbidden (missing permission)
- `404` - Not Found
- `422` - Validation failed

See [Error Responses](./ADMIN_WORKFLOW_FLOWS.md#error-response-format)

### Need Help?
1. Check the relevant documentation file
2. Search for your issue in Troubleshooting
3. Review code examples
4. Check database schema

---

## 🔄 Version History

**Current Version**: 1.0  
**Last Updated**: January 21, 2026  
**Framework**: Laravel 9+  
**Database**: MySQL

---

## 📄 Document Map

| Document | Size | Focus | Best For |
|----------|------|-------|----------|
| ADMIN_DOCUMENTATION.md | ~100KB | Comprehensive | In-depth learning |
| ADMIN_QUICK_REFERENCE.md | ~20KB | Quick lookup | Fast reference |
| ADMIN_WORKFLOW_FLOWS.md | ~30KB | Visual guides | Understanding flows |

---

## 🎯 Next Steps

1. **If you're new**: Start with [Full Documentation](./ADMIN_DOCUMENTATION.md)
2. **If you need an endpoint**: Check [Quick Reference](./ADMIN_QUICK_REFERENCE.md#api-quick-reference)
3. **If you want to understand flows**: Review [Workflow Flows](./ADMIN_WORKFLOW_FLOWS.md)
4. **If you're building**: Use the [Usage Examples](./ADMIN_DOCUMENTATION.md#usage-examples)

---

## 📧 Questions?

Refer to documentation files first. They contain:
- 60+ API endpoints
- Complete code examples
- Flow diagrams
- Architecture explanations
- Troubleshooting guides
- Permission lists
- Curl examples

**Everything you need is in these docs! 🚀**
