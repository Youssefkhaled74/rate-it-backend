# Vendor Update Guidelines

## What CAN Be Updated

When updating a vendor account, admins can modify:

✅ **name** - Vendor contact person name
✅ **email** - Email address  
✅ **password** - Password (must provide password_confirmation)
✅ **photo** - Profile photo/avatar
✅ **is_active** - Account status (true/false)

## What CANNOT Be Updated (Immutable Fields)

These fields are set at creation and cannot be changed:

❌ **brand_id** - Vendor's assigned brand (set once at creation)
❌ **phone** - Phone number (used for login/authentication)
❌ **role** - Always VENDOR_ADMIN (set at creation, cannot be changed)

## Why These Fields Are Immutable

1. **brand_id**: Once a vendor is assigned to a brand, changing it would require complex cascade logic (moving branches, reassigning staff, reviewing transaction history). This should be handled as a separate process with proper auditing.

2. **phone**: Phone is the primary identifier for vendor login. Changing it would break authentication and is too risky. If a vendor needs a new phone, create a new account.

3. **role**: The role is fixed at VENDOR_ADMIN creation. This ensures clear permission boundaries and prevents accidental privilege changes.

## Update Request Example

**PATCH** `/api/v1/admin/vendors/{vendor_id}`

**Body (form-data):**
```
name: "New Name"
email: "newemail@example.com"
password: "NewPassword123"
password_confirmation: "NewPassword123"
photo: [image file]
is_active: true
```

**Headers:**
```
Authorization: Bearer {{admin_token}}
Accept: application/json
```

## Notes

- All fields in update are **optional** - send only what you want to change
- Only send `password_confirmation` if you're changing the password
- Photo is optional; omit the field if not uploading a new photo
- The vendor's brand and role remain unchanged regardless of what you send
