# API Testing Guide - Login & Register

## Setup

Before testing, make sure:
1. Laravel server running: `php artisan serve`
2. Database migrated: `php artisan migrate --seed`
3. API is accessible at `http://localhost:8000`

## Test Cases

### 1. Register - Success (Status 201)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@arwanacitra.com",
    "phone": "081234567890",
    "department_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response (201 Created):**
```json
{
  "message": "Register success",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "john@arwanacitra.com",
    "phone": "081234567890",
    "department_id": 1,
    "created_at": "2026-01-30T10:30:00Z",
    "updated_at": "2026-01-30T10:30:00Z"
  },
  "token": "1|abc123def456xyz789...",
  "roles": ["requester"],
  "permissions": ["ticket.create", "ticket.view"]
}
```

---

### 2. Register - Duplicate Email (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "john@arwanacitra.com",
    "phone": "089876543210",
    "department_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 3. Register - Duplicate Phone (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Another User",
    "email": "another@arwanacitra.com",
    "phone": "081234567890",
    "department_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The phone has already been taken.",
  "errors": {
    "phone": ["The phone has already been taken."]
  }
}
```

---

### 4. Register - Invalid Email (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Invalid Email User",
    "email": "not-an-email",
    "phone": "082222222222",
    "department_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The email field must be a valid email address.",
  "errors": {
    "email": ["The email field must be a valid email address."]
  }
}
```

---

### 5. Register - Password Too Short (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Short Pass User",
    "email": "shortpass@arwanacitra.com",
    "phone": "082333333333",
    "department_id": 1,
    "password": "pass",
    "password_confirmation": "pass"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The password field must be at least 8 characters.",
  "errors": {
    "password": ["The password field must be at least 8 characters."]
  }
}
```

---

### 6. Register - Password Mismatch (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mismatch Pass User",
    "email": "mismatch@arwanacitra.com",
    "phone": "082444444444",
    "department_id": 1,
    "password": "password123",
    "password_confirmation": "differentpassword"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The password field confirmation does not match.",
  "errors": {
    "password": ["The password field confirmation does not match."]
  }
}
```

---

### 7. Register - Missing Required Fields (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Missing Fields User"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The email field is required. (and 2 more errors)",
  "errors": {
    "email": ["The email field is required."],
    "phone": ["The phone field is required."],
    "password": ["The password field is required."],
    "department_id": ["The department id field is required."]
  }
}
```

---

### 8. Login - Success with Email (Status 200)

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "john@arwanacitra.com",
    "password": "password123"
  }'
```

**Expected Response (200 OK):**
```json
{
  "message": "Login success",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "john@arwanacitra.com",
    "phone": "081234567890",
    "department_id": 1,
    "created_at": "2026-01-30T10:30:00Z",
    "updated_at": "2026-01-30T10:30:00Z"
  },
  "token": "2|xyz789abc123def456...",
  "roles": ["requester"],
  "permissions": ["ticket.create", "ticket.view"]
}
```

---

### 9. Login - Success with Phone (Status 200)

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "081234567890",
    "password": "password123"
  }'
```

**Expected Response (200 OK):**
```json
{
  "message": "Login success",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "john@arwanacitra.com",
    "phone": "081234567890",
    "department_id": 1,
    "created_at": "2026-01-30T10:30:00Z",
    "updated_at": "2026-01-30T10:30:00Z"
  },
  "token": "3|def456xyz789abc123...",
  "roles": ["requester"],
  "permissions": ["ticket.create", "ticket.view"]
}
```

---

### 10. Login - Invalid Credentials (Status 401)

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "john@arwanacitra.com",
    "password": "wrongpassword"
  }'
```

**Expected Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

---

### 11. Login - User Not Found (Status 401)

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "nonexistent@arwanacitra.com",
    "password": "password123"
  }'
```

**Expected Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

---

### 12. Login - Missing Credentials (Status 422)

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "john@arwanacitra.com"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "message": "The password field is required.",
  "errors": {
    "password": ["The password field is required."]
  }
}
```

---

## Testing with Postman

### 1. Create Collection "Ticketing System"

### 2. Create Environment Variables
```
{
  "base_url": "http://localhost:8000",
  "token": "saved_from_login_response",
  "user_email": "john@arwanacitra.com",
  "user_phone": "081234567890",
  "user_password": "password123"
}
```

### 3. Create Requests

#### Register Request
```
POST {{base_url}}/api/register
Content-Type: application/json

{
  "name": "Test User {{$timestamp}}",
  "email": "test{{$timestamp}}@arwanacitra.com",
  "phone": "082{{$randomInt}}",
  "department_id": 1,
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login Request
```
POST {{base_url}}/api/login
Content-Type: application/json

{
  "login": "{{user_email}}",
  "password": "{{user_password}}"
}
```

### 4. Tests Tab (Save Token Automatically)
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
  var jsonData = pm.response.json();
  pm.environment.set("token", jsonData.token);
}
```

---

## Quick Test Script

**save as `test-api.sh`:**
```bash
#!/bin/bash

BASE_URL="http://localhost:8000"
EMAIL="test$(date +%s)@arwanacitra.com"
PHONE="082$(shuf -i 0000000000-9999999999 | head -1)"

echo "=== Testing Register ==="
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/api/register" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test User\",
    \"email\": \"$EMAIL\",
    \"phone\": \"$PHONE\",
    \"department_id\": 1,
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\"
  }")

echo $REGISTER_RESPONSE | jq .

TOKEN=$(echo $REGISTER_RESPONSE | jq -r '.token')
echo "Token: $TOKEN"

echo -e "\n=== Testing Login ==="
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/api/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"login\": \"$EMAIL\",
    \"password\": \"password123\"
  }")

echo $LOGIN_RESPONSE | jq .

echo -e "\n=== Testing Protected Route (GET /me) ==="
curl -s -X GET "$BASE_URL/api/me" \
  -H "Authorization: Bearer $TOKEN" | jq .
```

**Run:**
```bash
chmod +x test-api.sh
./test-api.sh
```

---

## Common Issues & Solutions

### Issue: 404 Not Found for /api/login
**Solution:** Check `routes/api.php` - ensure routes are defined and route:cache is cleared
```bash
php artisan route:clear
```

### Issue: CORS error
**Solution:** Check `config/cors.php` and ensure credentials setting:
```php
'supports_credentials' => true,
```

### Issue: Token seems invalid
**Solution:** Check:
- Is token string valid and not empty?
- Is localStorage enabled?
- Check browser console for errors

### Issue: Wrong dashboard after login
**Solution:** Check:
- Roles returned from API
- TokenManager.getPrimaryRole() returns correct value
- Role to dashboard mapping in TokenManager

