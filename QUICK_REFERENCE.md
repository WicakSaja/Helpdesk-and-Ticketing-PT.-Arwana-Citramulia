# Login & Register - Quick Reference

## 📁 Files Created/Updated

### Created Files
- ✅ `public/js/auth-token-manager.js` - Token management utility
- ✅ `public/js/auth-form-handler.js` - Form handling & validation
- ✅ `LOGIN_REGISTER_DOCUMENTATION.md` - Full documentation
- ✅ `IMPLEMENTATION_SUMMARY.md` - Implementation overview
- ✅ `API_TESTING_GUIDE.md` - API test cases & examples

### Updated Files
- ✅ `resources/views/auth/register.blade.php` - Changed to JS form handler
- ✅ `resources/views/auth/login.blade.php` - Simplified to use shared handlers

---

## 🎯 Key Features

### Login Form
```html
<form onsubmit="handleLogin(event)">
  <input type="email" id="email" required>
  <input type="password" id="password" required>
  <button type="submit">MASUK SEKARANG</button>
</form>
```

**Features:**
- Accept email or phone as login field
- Validate email format
- Save token to localStorage
- Redirect to role-based dashboard

---

### Register Form
```html
<form onsubmit="handleRegister(event)">
  <input type="text" id="nameReg" required>
  <input type="number" id="phoneReg" required>
  <input type="email" id="emailReg" required>
  <select id="departmentSelect" required></select>
  <input type="password" id="passReg" required>
  <input type="password" id="passConfirm" required>
  <button type="submit">DAFTAR SEKARANG</button>
</form>
```

**Features:**
- All fields validated client-side
- Phone number format: 10-15 digits
- Password minimum 8 characters
- Department loaded from API
- Auto-assign "requester" role after registration

---

## 🔑 TokenManager API

```javascript
// Set token & user data
TokenManager.setToken(token, user, roles, permissions);

// Get token
const token = TokenManager.getToken();

// Get user data
const user = TokenManager.getUser();

// Get roles
const roles = TokenManager.getRoles();

// Check if authenticated
if (TokenManager.isAuthenticated()) { ... }

// Check specific role
if (TokenManager.hasRole('technician')) { ... }

// Get dashboard URL
const dashboardUrl = TokenManager.getDashboardUrl();

// Clear all data (logout)
TokenManager.clearToken();
```

---

## 📊 Data Validation Rules

### Register
| Field | Rule | Example |
|-------|------|---------|
| name | Required, string, max 100 | "John Doe" |
| email | Required, valid email, unique | "john@arwana.com" |
| phone | Required, string, max 20, unique | "081234567890" |
| department_id | Required, exists in departments | 1, 2, 3 |
| password | Required, min 8 chars | "password123" |
| password_confirmation | Required, must match password | "password123" |

### Login
| Field | Rule | Example |
|-------|------|---------|
| login | Required, email or phone | "john@arwana.com" or "081234567890" |
| password | Required, string | "password123" |

---

## 🎭 Role to Dashboard Mapping

| Role | Dashboard URL |
|------|---------------|
| master_admin | `/dashboard/admin` |
| helpdesk | `/dashboard/helpdesk` |
| supervisor | `/dashboard/supervisor` |
| technician | `/dashboard/technician` |
| requester | `/dashboard/requester` |

---

## 🔌 API Endpoints

### POST `/api/register`
```json
Request Body:
{
  "name": "string",
  "email": "string (email)",
  "phone": "string",
  "department_id": "integer",
  "password": "string (min 8)",
  "password_confirmation": "string"
}

Response (201):
{
  "message": "Register success",
  "user": {...},
  "token": "string",
  "roles": ["requester"],
  "permissions": [...]
}
```

### POST `/api/login`
```json
Request Body:
{
  "login": "string (email or phone)",
  "password": "string"
}

Response (200):
{
  "message": "Login success",
  "user": {...},
  "token": "string",
  "roles": ["technician"],
  "permissions": [...]
}
```

### GET `/api/departments`
```json
Response (200):
{
  "data": [
    {"id": 1, "name": "IT"},
    {"id": 2, "name": "HR"},
    ...
  ]
}
```

---

## 🧪 Testing Checklist

### Register Tests
- [ ] Success: Create new user with valid data
- [ ] Error: Duplicate email
- [ ] Error: Duplicate phone
- [ ] Error: Invalid email format
- [ ] Error: Password < 8 chars
- [ ] Error: Password mismatch
- [ ] Error: Missing required fields
- [ ] Verify: Token saved to localStorage
- [ ] Verify: Redirected to `/dashboard/requester`

### Login Tests
- [ ] Success: Login with email
- [ ] Success: Login with phone
- [ ] Error: Wrong password
- [ ] Error: Non-existent email
- [ ] Error: Missing password
- [ ] Verify: Token saved to localStorage
- [ ] Verify: Roles saved to localStorage
- [ ] Verify: Redirected to correct dashboard based on role

### UI Tests
- [ ] Password toggle visibility works
- [ ] Department dropdown loads from API
- [ ] Loading state shows during submit
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Form clears on successful submit

---

## 🚀 How It Works (Step by Step)

### Register Flow
1. User fills register form
2. Click "DAFTAR SEKARANG" button
3. `handleRegister(event)` is called
4. Client-side validation checks all fields
5. If valid, POST request sent to `/api/register`
6. Backend validates and creates user
7. Response includes token and roles
8. Token saved to localStorage via `TokenManager.setToken()`
9. Page redirects to dashboard
10. Dashboard route checks `TokenManager.isAuthenticated()`

### Login Flow
1. User fills login form (email or phone)
2. Click "MASUK SEKARANG" button
3. `handleLogin(event)` is called
4. Client-side validation checks email format and password
5. POST request sent to `/api/login` with "login" field
6. Backend detects if login is email or phone
7. User authenticated and token created
8. Response includes token, roles, and permissions
9. Token saved to localStorage
10. `TokenManager.getDashboardUrl()` determines dashboard based on first role
11. Page redirects to dashboard

---

## 🐛 Debugging Tips

### Check Token in Browser
```javascript
// In browser DevTools Console
TokenManager.getToken()      // Get token
TokenManager.getUser()       // Get user data
TokenManager.getRoles()      // Get roles
TokenManager.isAuthenticated() // Check auth status
```

### Check localStorage
DevTools → Application → Storage → Local Storage → Your domain
- `auth_token` - Sanctum token
- `auth_user` - User object JSON
- `auth_roles` - Roles array JSON
- `auth_permissions` - Permissions array JSON

### Monitor Network Requests
DevTools → Network tab → Filter for "/api/register" or "/api/login"
- Check request body is correct
- Check response status code (201 for register, 200 for login)
- Check response includes token

### Check Errors
DevTools → Console → Check for JavaScript errors
- "API_URL is not defined" → Layout not including script that sets API_URL
- "fetch failed" → API server not running or CORS issue
- "Token tidak valid" → Token response format issue

---

## 📋 Implementation Checklist

- [x] Create TokenManager utility
- [x] Create form handler functions
- [x] Update register.blade.php
- [x] Update login.blade.php
- [x] Client-side validation
- [x] API integration
- [x] Token storage
- [x] Role-based routing
- [ ] Setup dashboard routes for each role
- [ ] Create logout functionality
- [ ] Add password reset feature
- [ ] Setup email verification
- [ ] Add token refresh logic
- [ ] Security hardening

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue: "API_URL is not defined"**
- Fix: Ensure `custom-auth.blade.php` has the API_URL script tag

**Issue: Departments not loading**
- Check: Is `/api/departments` endpoint available?
- Check: Does API return data in correct format?

**Issue: Token not saving**
- Check: Is localStorage enabled in browser?
- Check: No CSP violations in console?
- Check: Token is valid string?

**Issue: Wrong dashboard after login**
- Check: Is role returned from API?
- Check: Role matches one in TokenManager mapping?
- Check: Dashboard route exists?

**Issue: CORS error**
- Check: Is CORS configured in `config/cors.php`?
- Check: API domain in whitelist?
- Check: credentials set to true?

---

## 📚 Documentation References

- [Full Documentation](LOGIN_REGISTER_DOCUMENTATION.md)
- [API Testing Guide](API_TESTING_GUIDE.md)
- [Implementation Summary](IMPLEMENTATION_SUMMARY.md)
- [Laravel Sanctum Docs](https://laravel.com/docs/sanctum)
- [Laravel Auth Docs](https://laravel.com/docs/authentication)

