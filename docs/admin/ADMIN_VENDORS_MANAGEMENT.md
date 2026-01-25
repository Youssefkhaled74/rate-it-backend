# Admin Vendors Management - Postman Collection Guide

## Overview

The **Admin Vendors** module allows administrators to create, manage, and monitor vendor (VENDOR_ADMIN) accounts across all brands.

## API Endpoints

### 1. **List All Vendors**
```
GET /api/v1/admin/vendors
```

**Query Parameters:**
- `brand_id` (optional) - Filter by brand
- `search` (optional) - Search by name, phone, or email
- `is_active` (optional) - Filter by active status (true/false)
- `per_page` (optional) - Items per page (default: 20)
- `page` (optional) - Page number

**Response:**
```json
{
  "success": true,
  "message": "admin.vendors.list",
  "data": [
    {
      "id": 1,
      "brand_id": 5,
      "brand": {
        "id": 5,
        "name_en": "McDonald's",
        "name_ar": "ماكدونالدز"
      },
      "name": "Ahmed Al-Khaldi",
      "phone": "+971501234567",
      "email": "ahmed@mcdonalds.ae",
      "role": "VENDOR_ADMIN",
      "is_active": true,
      "created_at": "2026-01-25T10:30:00Z",
      "updated_at": "2026-01-25T10:30:00Z"
    }
  ],
  "meta": {
    "total": 5,
    "per_page": 20,
    "current_page": 1
  }
}
```

---

### 2. **Get Vendor Details**
```
GET /api/v1/admin/vendors/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "admin.vendors.details",
  "data": {
    "id": 1,
    "brand_id": 5,
    "brand": {
      "id": 5,
      "name_en": "McDonald's",
      "name_ar": "ماكدونالدز"
    },
    "name": "Ahmed Al-Khaldi",
    "phone": "+971501234567",
    "email": "ahmed@mcdonalds.ae",
    "role": "VENDOR_ADMIN",
    "is_active": true,
    "created_at": "2026-01-25T10:30:00Z",
    "updated_at": "2026-01-25T10:30:00Z"
  }
}
```

---

### 3. **Create Vendor Account**
```
POST /api/v1/admin/vendors
```

**Request Body:**
```json
{
  "brand_id": 5,
  "name": "Ahmed Al-Khaldi",
  "phone": "+971501234567",
  "email": "ahmed@mcdonalds.ae",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123"
}
```

**Validation Rules:**
- `brand_id` - required, must exist in brands table
- `name` - required, string, max 255 characters
- `phone` - required, unique in vendor_users, regex pattern, max 20 chars
- `email` - optional, unique in vendor_users, valid email format
- `password` - required, minimum 6 characters
- `password_confirmation` - must match password

**Response (201 Created):**
```json
{
  "success": true,
  "message": "admin.vendors.created",
  "data": {
    "id": 1,
    "brand_id": 5,
    "brand": {
      "id": 5,
      "name_en": "McDonald's",
      "name_ar": "ماكدونالدز"
    },
    "name": "Ahmed Al-Khaldi",
    "phone": "+971501234567",
    "email": "ahmed@mcdonalds.ae",
    "role": "VENDOR_ADMIN",
    "is_active": true,
    "created_at": "2026-01-25T10:30:00Z",
    "updated_at": "2026-01-25T10:30:00Z"
  }
}
```

**Error Cases:**
```json
{
  "success": false,
  "message": "Validation failed",
  "data": {
    "errors": {
      "phone": ["Phone number already registered"],
      "email": ["Email already registered"]
    }
  }
}
```

---

### 4. **Update Vendor Details**
```
PATCH /api/v1/admin/vendors/{id}
```

**Request Body (All fields optional):**
```json
{
  "name": "Ahmed Al-Khaldi Updated",
  "email": "newemail@example.com",
  "password": "NewSecurePass123",
  "password_confirmation": "NewSecurePass123",
  "is_active": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "admin.vendors.updated",
  "data": {
    "id": 1,
    "brand_id": 5,
    "name": "Ahmed Al-Khaldi Updated",
    "phone": "+971501234567",
    "email": "newemail@example.com",
    "role": "VENDOR_ADMIN",
    "is_active": true,
    "created_at": "2026-01-25T10:30:00Z",
    "updated_at": "2026-01-25T10:35:00Z"
  }
}
```

---

### 5. **Delete Vendor (Soft Delete)**
```
DELETE /api/v1/admin/vendors/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "admin.vendors.deleted",
  "data": null
}
```

---

### 6. **Restore Deleted Vendor**
```
POST /api/v1/admin/vendors/{id}/restore
```

**Response:**
```json
{
  "success": true,
  "message": "admin.vendors.restored",
  "data": {
    "id": 1,
    "brand_id": 5,
    "name": "Ahmed Al-Khaldi",
    "phone": "+971501234567",
    "email": "ahmed@mcdonalds.ae",
    "role": "VENDOR_ADMIN",
    "is_active": true,
    "created_at": "2026-01-25T10:30:00Z",
    "updated_at": "2026-01-25T10:40:00Z"
  }
}
```

---

## Postman Collection Requests

### Import to Postman

Add this folder to your Admin API Postman collection:

```json
{
  "name": "05 - Vendors Management",
  "item": [
    {
      "name": "List Vendors",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors?per_page=20&page=1",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors"],
          "query": [
            {"key": "per_page", "value": "20"},
            {"key": "page", "value": "1"},
            {"key": "brand_id", "value": "", "disabled": true},
            {"key": "search", "value": "", "disabled": true},
            {"key": "is_active", "value": "true", "disabled": true}
          ]
        },
        "description": "List all vendor admin accounts with optional filtering"
      },
      "response": []
    },
    {
      "name": "Get Vendor Details",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors/1",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "1"]
        },
        "description": "Get details of a specific vendor"
      },
      "response": []
    },
    {
      "name": "Create Vendor",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          },
          {
            "key": "Content-Type",
            "value": "application/json",
            "type": "text"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"brand_id\": 5,\n  \"name\": \"Ahmed Al-Khaldi\",\n  \"phone\": \"+971501234567\",\n  \"email\": \"ahmed@example.com\",\n  \"password\": \"SecurePass123\",\n  \"password_confirmation\": \"SecurePass123\"\n}"
        },
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors"]
        },
        "description": "Create a new vendor admin account for a brand"
      },
      "response": []
    },
    {
      "name": "Update Vendor",
      "request": {
        "method": "PATCH",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          },
          {
            "key": "Content-Type",
            "value": "application/json",
            "type": "text"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"name\": \"Ahmed Al-Khaldi Updated\",\n  \"email\": \"newemail@example.com\",\n  \"is_active\": true\n}"
        },
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors/1",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "1"]
        },
        "description": "Update vendor details (name, email, password, or status)"
      },
      "response": []
    },
    {
      "name": "Delete Vendor",
      "request": {
        "method": "DELETE",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors/1",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "1"]
        },
        "description": "Soft delete a vendor account (can be restored)"
      },
      "response": []
    },
    {
      "name": "Restore Vendor",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors/1/restore",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "1", "restore"]
        },
        "description": "Restore a previously deleted vendor account"
      },
      "response": []
    }
  ]
}
```

---

## Usage Examples

### Example 1: Create a vendor for McDonald's brand
```bash
curl -X POST http://localhost:8000/api/v1/admin/vendors \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "brand_id": 5,
    "name": "Ahmed Al-Khaldi",
    "phone": "+971501234567",
    "email": "ahmed@mcdonalds.ae",
    "password": "SecurePass123",
    "password_confirmation": "SecurePass123"
  }'
```

### Example 2: List all vendors for a specific brand
```bash
curl -X GET "http://localhost:8000/api/v1/admin/vendors?brand_id=5" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

### Example 3: Update vendor details
```bash
curl -X PATCH http://localhost:8000/api/v1/admin/vendors/1 \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed Updated",
    "email": "newemail@example.com"
  }'
```

---

## Testing Scenarios

| Scenario | Method | Endpoint | Expected Status |
|----------|--------|----------|-----------------|
| List all vendors | GET | /api/v1/admin/vendors | 200 |
| Get vendor details | GET | /api/v1/admin/vendors/1 | 200 |
| Create vendor (valid) | POST | /api/v1/admin/vendors | 201 |
| Create vendor (duplicate phone) | POST | /api/v1/admin/vendors | 422 |
| Create vendor (invalid brand) | POST | /api/v1/admin/vendors | 422 |
| Update vendor | PATCH | /api/v1/admin/vendors/1 | 200 |
| Delete vendor | DELETE | /api/v1/admin/vendors/1 | 200 |
| Restore vendor | POST | /api/v1/admin/vendors/1/restore | 200 |
| Get non-existent vendor | GET | /api/v1/admin/vendors/99999 | 404 |

---

## Features

✅ **Create vendor accounts** - Admin creates VENDOR_ADMIN accounts  
✅ **List & filter vendors** - View all vendors by brand, search by name/phone/email  
✅ **Update vendor details** - Change name, email, password, active status  
✅ **Soft delete** - Vendors can be deactivated (soft deleted)  
✅ **Restore deleted vendors** - Re-activate previously deleted accounts  
✅ **Validation** - Phone uniqueness, email format, password confirmation  
✅ **Role enforcement** - Created account is always VENDOR_ADMIN role  
✅ **Multi-language support** - English and Arabic error messages  

---

## Integration with Vendor Module

Once a vendor account is created by admin:

1. **Vendor can login** with their phone and password
2. **Vendor can create staff** (BRANCH_STAFF accounts) for their branches
3. **Vendor can manage reviews, vouchers, and dashboard**
4. **All vendor data is scoped to their brand**

---

## Security

✅ **Token required** - All endpoints require Bearer token  
✅ **Password hashed** - Passwords stored with bcrypt  
✅ **Soft deletes** - Deleted vendors can be recovered  
✅ **Unique phone** - Prevents duplicate phone numbers  
✅ **Email validation** - Valid email format if provided  
✅ **Password confirmation** - Must match on creation  

