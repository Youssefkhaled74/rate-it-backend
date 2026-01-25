# How to Update Postman Collection - Vendors Module

## Quick Summary

Add this folder to your **Admin API Postman collection** under the main "item" array:

```json
{
  "name": "05 - Vendors Management",
  "item": [
    {
      "name": "List Vendors",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 200', function() {",
              "    pm.response.to.have.status(200);",
              "});",
              "",
              "pm.test('Response has vendor_id', function() {",
              "    const jsonData = pm.response.json();",
              "    if (jsonData.data && jsonData.data.length > 0) {",
              "        pm.environment.set('vendor_id', jsonData.data[0].id);",
              "    }",
              "});",
              "",
              "pm.test('Response structure', function() {",
              "    pm.response.to.have.jsonBody('success');",
              "    pm.response.to.have.jsonBody('data');",
              "    pm.response.to.have.jsonBody('meta');",
              "});"
            ]
          }
        }
      ],
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
            {
              "key": "per_page",
              "value": "20"
            },
            {
              "key": "page",
              "value": "1"
            },
            {
              "key": "brand_id",
              "value": "",
              "disabled": true
            },
            {
              "key": "search",
              "value": "",
              "disabled": true
            },
            {
              "key": "is_active",
              "value": "true",
              "disabled": true
            }
          ]
        },
        "description": "List all vendor admin accounts with optional filtering by brand, search, or active status"
      },
      "response": []
    },
    {
      "name": "Get Vendor Details",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 200', function() {",
              "    pm.response.to.have.status(200);",
              "});",
              "",
              "pm.test('Response has vendor data', function() {",
              "    pm.response.to.have.jsonBody('data.id');",
              "    pm.response.to.have.jsonBody('data.name');",
              "    pm.response.to.have.jsonBody('data.phone');",
              "    pm.response.to.have.jsonBody('data.role');",
              "});"
            ]
          }
        }
      ],
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
          "raw": "{{base_url}}/api/v1/admin/vendors/{{vendor_id}}",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "{{vendor_id}}"]
        },
        "description": "Get details of a specific vendor admin account"
      },
      "response": []
    },
    {
      "name": "Create Vendor",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 201', function() {",
              "    pm.response.to.have.status(201);",
              "});",
              "",
              "pm.test('Vendor created with VENDOR_ADMIN role', function() {",
              "    const jsonData = pm.response.json();",
              "    pm.expect(jsonData.data.role).to.equal('VENDOR_ADMIN');",
              "    pm.expect(jsonData.data.is_active).to.equal(true);",
              "    pm.environment.set('vendor_id', jsonData.data.id);",
              "});",
              "",
              "pm.test('Password not returned', function() {",
              "    const jsonData = pm.response.json();",
              "    pm.expect(jsonData.data).to.not.have.property('password');",
              "});"
            ]
          }
        }
      ],
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
          "raw": "{\n  \"brand_id\": {{brand_id}},\n  \"name\": \"Ahmed Al-Khaldi\",\n  \"phone\": \"+971501234567\",\n  \"email\": \"ahmed@example.com\",\n  \"password\": \"SecurePass123\",\n  \"password_confirmation\": \"SecurePass123\"\n}"
        },
        "url": {
          "raw": "{{base_url}}/api/v1/admin/vendors",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors"]
        },
        "description": "Create a new vendor admin account for a specified brand. Password is auto-hashed."
      },
      "response": []
    },
    {
      "name": "Update Vendor",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 200', function() {",
              "    pm.response.to.have.status(200);",
              "});",
              "",
              "pm.test('Vendor updated successfully', function() {",
              "    const jsonData = pm.response.json();",
              "    pm.expect(jsonData.success).to.equal(true);",
              "    pm.expect(jsonData.data.name).to.equal('Ahmed Al-Khaldi Updated');",
              "});"
            ]
          }
        }
      ],
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
          "raw": "{{base_url}}/api/v1/admin/vendors/{{vendor_id}}",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "{{vendor_id}}"]
        },
        "description": "Update vendor details like name, email, password, or active status. All fields are optional."
      },
      "response": []
    },
    {
      "name": "Delete Vendor",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 200', function() {",
              "    pm.response.to.have.status(200);",
              "});",
              "",
              "pm.test('Vendor deleted successfully', function() {",
              "    const jsonData = pm.response.json();",
              "    pm.expect(jsonData.success).to.equal(true);",
              "});"
            ]
          }
        }
      ],
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
          "raw": "{{base_url}}/api/v1/admin/vendors/{{vendor_id}}",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "{{vendor_id}}"]
        },
        "description": "Soft delete a vendor account (can be restored later)"
      },
      "response": []
    },
    {
      "name": "Restore Vendor",
      "event": [
        {
          "listen": "test",
          "script": {
            "type": "text/javascript",
            "exec": [
              "pm.test('Status code is 200', function() {",
              "    pm.response.to.have.status(200);",
              "});",
              "",
              "pm.test('Vendor restored successfully', function() {",
              "    const jsonData = pm.response.json();",
              "    pm.expect(jsonData.success).to.equal(true);",
              "    pm.expect(jsonData.message).to.equal('admin.vendors.restored');",
              "});"
            ]
          }
        }
      ],
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
          "raw": "{{base_url}}/api/v1/admin/vendors/{{vendor_id}}/restore",
          "protocol": "https",
          "host": ["{{base_url}}"],
          "path": ["api", "v1", "admin", "vendors", "{{vendor_id}}", "restore"]
        },
        "description": "Restore a previously deleted (soft-deleted) vendor account"
      },
      "response": []
    }
  ]
}
```

## Manual Steps to Add to Postman

1. **Open Postman**
2. **Open Admin API collection**
3. **Right-click on collection** → "Edit"
4. **Find the "item" array** (main request folders)
5. **Locate "04 - Users Management"** folder
6. **After closing that folder**, add a comma and paste the entire folder above
7. **Save** the collection
8. **Close edit mode**

## Postman Variables

Make sure these variables are set in your collection:
- `base_url` = `http://localhost:8000`
- `admin_token` = Your admin Bearer token
- `brand_id` = ID of brand to use for testing
- `vendor_id` = Will be auto-set by "List Vendors" or "Create Vendor" tests

## Testing the Collection

1. **Login first** - Run the auth login request to get `admin_token`
2. **Run List Vendors** - Gets vendors and sets `vendor_id`
3. **Create Vendor** - Creates new vendor and sets `vendor_id`
4. **Get Details** - Uses `vendor_id` variable
5. **Update Vendor** - Modifies the vendor
6. **Delete Vendor** - Soft deletes
7. **Restore Vendor** - Restores deleted vendor

All requests have **test scripts** that automatically check response status and structure!

---

**Total Requests Added: 6**
**Location in Collection: 05 - Vendors Management**

