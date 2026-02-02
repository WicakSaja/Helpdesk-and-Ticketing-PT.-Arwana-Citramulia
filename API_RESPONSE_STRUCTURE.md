# API Response Structure Documentation

## Overview
Setelah modularisasi backend, semua endpoint API mengikuti struktur response yang konsisten dan mudah dibaca (flat structure - tanpa nested roles/permissions di object user).

## Auth Endpoints

### POST /api/register
**Response:**
```json
{
  "message": "Register success",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "phone": "081234567890",
    "department_id": 1,
    "is_active": true,
    "created_at": "2026-02-02T02:56:29.000000Z",
    "updated_at": "2026-02-02T02:56:29.000000Z"
  },
  "token": "124|qcXojALaw5adF718pkMVxBap7xJynHrF8icFhhQb6744136f"
}
```

**Frontend Usage:**
```javascript
const user = data.user;
const token = data.token;

// Access user data
console.log(user.name, user.email);

// Store token
TokenManager.setAuth(token, user);
```

---

### POST /api/login
**Response:**
```json
{
  "message": "Login success",
  "user": {
    "id": 1,
    "name": "MasterAdmin",
    "email": "admin@company.local",
    "phone": "6282232305078",
    "department_id": null,
    "is_active": true,
    "created_at": "2026-01-28T09:02:04.000000Z",
    "updated_at": "2026-01-28T09:02:04.000000Z"
  },
  "roles": ["master-admin"],
  "permissions": [
    "ticket.create",
    "ticket.view",
    "ticket.view.all",
    "ticket.comment",
    "ticket.assign",
    "ticket.change_status",
    "ticket.resolve",
    "ticket.close",
    "ticket.escalate",
    "ticket.view.dashboard",
    "ticket.view.report",
    "user.view",
    "user.view.all",
    "user.create",
    "user.update",
    "user.delete",
    "user.assign_role",
    "user.assign_permission"
  ],
  "token": "124|qcXojALaw5adF718pkMVxBap7xJynHrF8icFhhQb6744136f"
}
```

**Key Points:**
- `user` object: Basic user information (NO nested roles/permissions)
- `roles`: Array of strings, e.g., `["master-admin"]`
- `permissions`: Array of permission strings, e.g., `["ticket.create", "ticket.view"]`
- `token`: Sanctum API token for Bearer authentication

**Frontend Usage:**
```javascript
// Login response
const { user, roles, permissions, token } = data;

// Store all data
TokenManager.setAuth(token, user, roles);

// Check permissions
if (permissions.includes('user.view')) {
  // Show user management
}
```

---

### GET /api/me
**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "MasterAdmin",
    "email": "admin@company.local",
    "phone": "6282232305078",
    "department_id": null,
    "is_active": true,
    "created_at": "2026-01-28T09:02:04.000000Z",
    "updated_at": "2026-01-28T09:02:04.000000Z"
  },
  "roles": ["master-admin"],
  "permissions": [
    "ticket.create",
    "ticket.view",
    ...
  ]
}
```

---

## User Management Endpoints

### GET /api/users
**Response:**
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
      {
        "id": 2,
        "name": "Teknisi",
        "email": "tech@arwana.com",
        "phone": "0812345678910",
        "department_id": 1,
        "department": {
          "id": 1,
          "name": "produksi",
          "created_at": "2026-01-28T09:02:04.000000Z",
          "updated_at": "2026-01-28T09:02:04.000000Z"
        },
        "roles": ["technician"],
        "is_active": true,
        "created_at": "2026-01-29T01:19:19.000000Z",
        "updated_at": "2026-01-29T01:19:19.000000Z"
      }
    ],
    "first_page_url": "http://127.0.0.1:8000/api/users?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "http://127.0.0.1:8000/api/users?page=2",
    "links": [...],
    "next_page_url": "http://127.0.0.1:8000/api/users?page=2",
    "path": "http://127.0.0.1:8000/api/users",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 20
  }
}
```

**Frontend Usage - Accessing roles as strings:**
```javascript
const users = result.data.data;

users.forEach(user => {
  // roles is array of strings
  const primaryRole = user.roles[0]; // e.g., "master-admin"
  const roleName = formatRoleName(primaryRole); // "Master Admin"
  const roleClass = getRoleClass(primaryRole); // "role-admin"
});
```

---

### GET /api/users/{id}
**Response:**
```json
{
  "message": "User retrieved successfully",
  "data": {
    "id": 19,
    "name": "Test User",
    "email": "testuser@test.com",
    "phone": "081234567890",
    "department_id": 1,
    "department": {
      "id": 1,
      "name": "produksi",
      "created_at": "2026-01-28T09:02:04.000000Z",
      "updated_at": "2026-01-28T09:02:04.000000Z"
    },
    "roles": ["requester"],
    "is_active": true,
    "created_at": "2026-02-02T02:56:29.000000Z",
    "updated_at": "2026-02-02T02:56:29.000000Z"
  }
}
```

---

### GET /api/users/roles-summary
**Response:**
```json
{
  "message": "Roles summary retrieved successfully",
  "data": [
    {
      "role": "helpdesk",
      "user_count": 4
    },
    {
      "role": "technician",
      "user_count": 3
    },
    {
      "role": "supervisor",
      "user_count": 2
    },
    {
      "role": "requester",
      "user_count": 10
    },
    {
      "role": "manager",
      "user_count": 0
    },
    {
      "role": "master-admin",
      "user_count": 1
    }
  ]
}
```

---

## Ticket Endpoints

### POST /api/tickets
**Response:**
```json
{
  "message": "Ticket created",
  "ticket": {
    "ticket_number": "TKT-2026-000004",
    "requester_id": 19,
    "status_id": 1,
    "subject": "Test Ticket",
    "description": "Testing ticket creation",
    "category_id": 1,
    "channel": "web",
    "updated_at": "2026-02-02T02:58:20.000000Z",
    "created_at": "2026-02-02T02:58:20.000000Z",
    "id": 4
  }
}
```

---

### GET /api/tickets
**Response:**
```json
{
  "message": "Tickets retrieved successfully",
  "data": [
    {
      "id": 4,
      "ticket_number": "TKT-2026-000004",
      "requester_id": 19,
      "status_id": 1,
      "subject": "Test Ticket",
      "description": "Testing ticket creation",
      "channel": "web",
      "closed_at": null,
      "created_at": "2026-02-02T02:58:20.000000Z",
      "updated_at": "2026-02-02T02:58:20.000000Z",
      "category_id": 1,
      "status": {
        "id": 1,
        "name": "Open",
        "created_at": "2026-01-28 09:02:04",
        "updated_at": "2026-01-28 09:02:04"
      },
      "category": {
        "id": 1,
        "name": "Hardware",
        "description": "Masalah perangkat keras...",
        "created_at": "2026-01-28T09:02:04.000000Z",
        "updated_at": "2026-01-28T09:02:04.000000Z"
      },
      "requester": {
        "id": 19,
        "name": "Test User",
        "email": "testuser@test.com"
      },
      "assignment": null
    }
  ],
  "total": 1
}
```

---

## Category & Department Endpoints

### GET /api/categories
**Response:**
```json
{
  "message": "Daftar kategori berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Hardware",
      "description": "Masalah perangkat keras...",
      "created_at": "2026-01-28T09:02:04.000000Z",
      "updated_at": "2026-01-28T09:02:04.000000Z"
    }
  ],
  "total": 5
}
```

---

### GET /api/departments
**Response:**
```json
{
  "message": "Daftar departemen berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "produksi",
      "created_at": "2026-01-28T09:02:04.000000Z",
      "updated_at": "2026-01-28T09:02:04.000000Z"
    }
  ],
  "total": 3
}
```

---

## Form Validation Error Response

**Status Code: 422 Unprocessable Content**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "description": [
      "Deskripsi tiket minimal 10 karakter"
    ],
    "category_id": [
      "Kategori tiket tidak ditemukan"
    ],
    "channel": [
      "Channel tiket harus web, mobile, atau email"
    ]
  }
}
```

---

## Frontend Token Management

### Storing Token
```javascript
// After login/register
const token = response.data.token;
const user = response.data.user;
const roles = response.data.roles;

TokenManager.setAuth(token, user, roles);
// Stores in sessionStorage:
// - auth_token: token value
// - auth_user: user object JSON
// - auth_roles: roles array JSON
```

### Using Token in Requests
```javascript
function getAuthToken() {
  return sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token");
}

const headers = {
  Authorization: `Bearer ${getAuthToken()}`,
  "Content-Type": "application/json"
};
```

### Checking Permissions
```javascript
const roles = TokenManager.getRoles();
const permissions = TokenManager.getPermissions();

if (permissions.includes('user.view')) {
  // User can view user management
}
```

---

## Key Differences After Modularization

### Before (Nested Structure)
```json
{
  "user": {
    "roles": [
      {
        "id": 6,
        "name": "master-admin",
        "permissions": [
          {
            "id": 1,
            "name": "ticket.create"
          }
        ]
      }
    ]
  },
  "roles": ["master-admin"],
  "permissions": ["ticket.create"]
}
```

### After (Flat Structure) ✅
```json
{
  "user": {
    "id": 1,
    "name": "MasterAdmin",
    "email": "admin@company.local"
    // NO nested roles/permissions
  },
  "roles": ["master-admin"],
  "permissions": ["ticket.create"]
}
```

**Benefits:**
✅ Simpler structure - easier to read and debug
✅ Smaller response payload
✅ Faster data processing on frontend
✅ Less chance of circular references
✅ Cleaner separation between user data and access control

---

## Frontend Code Updates Required

### 1. Access Roles as Strings
**Old (Before):**
```javascript
const role = user.roles[0]; // This was an object: {id: 6, name: "master-admin", ...}
const roleName = role.name;
```

**New (After):**
```javascript
const role = user.roles[0]; // This is now a string: "master-admin"
const roleName = formatRoleName(role);
```

### 2. Updated Helper Functions
**users.js - formatRoleName():**
```javascript
function formatRoleName(role) {
  const nameMap = {
    "master-admin": "Master Admin",
    "helpdesk": "Helpdesk",
    "technician": "Technician",
    "requester": "Requester"
  };
  return nameMap[role] || role;
}
```

### 3. Handling Undefined Roles
```javascript
// Safety check for users with no roles
const primaryRole = user.roles && user.roles[0] ? user.roles[0] : 'user';
const roleName = formatRoleName(primaryRole);
```

---

## Backward Compatibility

✅ **All frontend code has been updated** to work with the new flat structure.
✅ **Token handling** remains unchanged - uses `Bearer` token in Authorization header.
✅ **Permission checking** still uses top-level `permissions` array.
✅ **Role-based UI** works with string role names via `formatRoleName()` and `getRoleClass()`.

