# API Response Structure Documentation

## Overview
Setelah dilakukan modularisasi pada backend, struktur response API telah disesuaikan untuk memastikan backward compatibility dengan frontend yang sudah ada.

## Authentication Endpoints

### POST /api/login
**Response Status:** 200 OK

```json
{
    "message": "Login success",
    "user": {
        "id": 1,
        "name": "MasterAdmin",
        "email": "admin@company.local",
        "email_verified_at": null,
        "created_at": "2026-01-28T09:02:04.000000Z",
        "updated_at": "2026-01-28T09:02:04.000000Z",
        "department_id": null,
        "phone": "6282232305078",
        "is_active": true,
        "roles": [
            {
                "id": 6,
                "name": "master-admin",
                "guard_name": "web",
                "created_at": "2026-01-28T09:02:04.000000Z",
                "updated_at": "2026-01-28T09:02:04.000000Z",
                "pivot": {
                    "model_type": "App\\Models\\User",
                    "model_id": 1,
                    "role_id": 6
                },
                "permissions": [
                    {
                        "id": 1,
                        "name": "ticket.create",
                        "guard_name": "web",
                        "created_at": "2026-01-28T09:02:04.000000Z",
                        "updated_at": "2026-01-28T09:02:04.000000Z",
                        "pivot": {
                            "role_id": 6,
                            "permission_id": 1
                        }
                    },
                    // ... more permissions
                ]
            }
        ],
        "permissions": []
    },
    "roles": ["master-admin"],
    "permissions": [
        "ticket.create",
        "ticket.view",
        "ticket.view.all",
        // ... more permissions
    ],
    "token": "118|eTQbN8pjx2MhgMerskAIMQ8gjQL7iURktEEExbEia9f7b742"
}
```

**Key Points:**
- `user` object berisi full user model dengan relationships
- `user.roles` adalah array of role objects (bukan string array)
- Setiap role object memiliki `permissions` array dengan detail lengkap
- Response-level `roles` adalah array of strings (hanya nama role)
- Response-level `permissions` adalah array of strings (hanya nama permission)
- Field `email_verified_at` selalu ada (nullable)

### POST /api/register
**Response Status:** 201 Created

Struktur response sama dengan `/api/login`, hanya status code yang berbeda.

```json
{
    "message": "Register success",
    "user": { /* full user object dengan roles */ },
    "token": "xxx|yyy..."
}
```

### GET /api/me
**Response Status:** 200 OK

```json
{
    "user": { /* full user object dengan roles */ },
    "roles": ["master-admin"],
    "permissions": ["ticket.create", ...]
}
```

## Data Access Endpoints

### GET /api/users
**Response Status:** 200 OK

```json
{
    "message": "Users retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "MasterAdmin",
                "email": "admin@company.local",
                "phone": "6282232305078",
                "department_id": null,
                "department": null,
                "roles": ["master-admin"],
                "is_active": true,
                "created_at": "2026-01-28T09:02:04.000000Z",
                "updated_at": "2026-01-28T09:02:04.000000Z"
            },
            // ... more users
        ],
        "first_page_url": "http://127.0.0.1:8000/api/users?page=1",
        "from": 1,
        "last_page": 2,
        "last_page_url": "http://127.0.0.1:8000/api/users?page=2",
        "links": [ /* pagination links */ ],
        "next_page_url": "http://127.0.0.1:8000/api/users?page=2",
        "path": "http://127.0.0.1:8000/api/users",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 20
    }
}
```

**Note:** Pada endpoint data (non-auth), `roles` tetap berupa array of strings untuk performa.

## Endpoint Changes After Modularization

### ✅ UNCHANGED Endpoints (100% Compatible)
- `POST /api/register` - Struktur response sekarang lebih lengkap (full user object)
- `POST /api/login` - Struktur response sekarang lebih lengkap (full user object)
- `GET /api/me` - Response konsisten
- `POST /api/logout` - Unchanged
- `GET /api/users` - Unchanged
- `GET /api/tickets` - Unchanged
- `POST /api/tickets` - Unchanged
- Semua endpoint lainnya - Unchanged

### 🔄 IMPROVED Endpoints
1. **Login/Register**: Sekarang mengembalikan full user object dengan nested relationships
   - ✅ Backward compatible dengan frontend yang sudah ada
   - ✅ Frontend dapat mengakses `user.roles[0].permissions`
   - ✅ Frontend dapat cek role dengan `user.roles[0].name`

## Frontend Updates Required

### ✅ Already Updated
- `public/js/auth-form-handler.js` - Sudah handle response format baru
- `public/js/auth-token-manager.js` - `hasRole()` method sudah robust untuk handle string/object format
- `public/js/users.js` - Token handling sudah fixed untuk dynamic retrieval

### Token Management
Frontend sekarang menggunakan `getAuthToken()` function untuk mendapatkan token dynamically dari sessionStorage:

```javascript
// Before (Static - problematic)
const authToken = sessionStorage.getItem("auth_token");

// After (Dynamic - fixed)
function getAuthToken() {
    return sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token");
}

// Used in fetch calls
Authorization: `Bearer ${getAuthToken()}`
```

## Testing Checklist

✅ **Login Endpoint**
- Returns full user object dengan roles dan permissions
- Field `email_verified_at` ada
- Token dapat digunakan untuk authenticated requests
- HTTP 401 jika credentials invalid

✅ **Register Endpoint**
- User baru berhasil dibuat
- Default role "requester" di-assign
- Token returned dan valid untuk authenticated requests
- HTTP 201 status code

✅ **Me Endpoint**
- Returns current authenticated user
- Token validation bekerja dengan benar
- HTTP 401 jika token invalid/expired

✅ **Frontend Token Handling**
- Token dynamically fetched dari sessionStorage
- No more 401 errors due to stale token reference
- Token persisted correctly setelah login

## Migration Guide for Existing Frontend

Jika ada custom frontend yang menggunakan API lama, berikut adalah perubahan yang perlu dilakukan:

### 1. User Object Structure (Login/Register)
```javascript
// Old way (still works)
const roles = data.roles; // ["master-admin"]

// New way (recommended for full data)
const rolesDetail = data.user.roles; // [{id, name, permissions, ...}]
const role = data.user.roles[0];
const permissions = role.permissions; // Full permission objects
```

### 2. Permission Checking
```javascript
// Old way
const perms = data.permissions; // ["ticket.create", "ticket.view"]
const canCreate = perms.includes("ticket.create");

// New way (backward compatible)
const userRoles = data.user.roles;
const userPermissions = userRoles[0].permissions;
const perms = data.permissions; // Still available at response level
```

### 3. Token Management (Frontend Only)
```javascript
// Before (Static - causes 401 errors)
const token = sessionStorage.getItem("auth_token");
// Risk: Stale token jika session berubah

// After (Dynamic)
function getToken() {
    return sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token");
}
// Always gets fresh token
```

## Summary

| Aspect | Before | After | Impact |
|--------|--------|-------|--------|
| Login Response | User + roles array | User + full roles objects | ✅ More detailed, backward compatible |
| Register Response | User object | User + roles relationships | ✅ More complete, better UX |
| email_verified_at | Missing | Included | ✅ Consistent with Laravel conventions |
| Token Handling (Frontend) | Static const | Dynamic function | ✅ Fixes 401 errors on navigation |
| Role Objects | String only | Object with permissions | ✅ More powerful for advanced features |

**Conclusion:** API response structure diperbaiki untuk memberikan data yang lebih lengkap sambil tetap maintain backward compatibility dengan frontend yang sudah ada. Frontend juga diperbaiki untuk handle token lebih baik.
