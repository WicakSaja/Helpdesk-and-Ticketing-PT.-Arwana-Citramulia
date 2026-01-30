# Login & Register Feature Documentation

## Overview
Sistem login dan register yang terintegrasi dengan API Laravel. Data yang sudah tervalidasi di frontend dikirim ke endpoint API untuk divalidasi kembali di backend, kemudian token dikembalikan dan disimpan di localStorage.

## Architecture

### Frontend Flow
1. **Register/Login Form** → User mengisi form dengan data
2. **Client-side Validation** → Validasi format email, phone, password di JavaScript
3. **API Call** → Data dikirim ke `/api/register` atau `/api/login`
4. **Token Management** → Token disimpan di localStorage via `TokenManager`
5. **Dashboard Redirect** → User diarahkan ke dashboard berdasarkan role

### Backend Response
```json
{
  "message": "Register success / Login success",
  "token": "sanctum_token_here",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "phone": "0812345678",
    "department_id": 1
  },
  "roles": ["requester"],
  "permissions": ["ticket.create", "ticket.view"]
}
```

## Implementation Details

### JavaScript Files

#### 1. `public/js/auth-token-manager.js`
Mengelola penyimpanan dan pengambilan token/user data dari localStorage.

**Key Methods:**
- `TokenManager.setToken(token, user, roles, permissions)` - Simpan token
- `TokenManager.getToken()` - Ambil token
- `TokenManager.getUser()` - Ambil data user
- `TokenManager.getRoles()` - Ambil daftar roles
- `TokenManager.isAuthenticated()` - Cek status login
- `TokenManager.getPrimaryRole()` - Ambil role utama
- `TokenManager.getDashboardUrl()` - Dapatkan URL dashboard sesuai role

**Role to Dashboard Mapping:**
```javascript
{
  'master_admin': '/dashboard/admin',
  'helpdesk': '/dashboard/helpdesk',
  'supervisor': '/dashboard/supervisor',
  'technician': '/dashboard/technician',
  'requester': '/dashboard/requester'
}
```

#### 2. `public/js/auth-form-handler.js`
Menangani form submission dan validasi data.

**Key Functions:**
- `handleRegister(event)` - Handle register form submission
- `handleLogin(event)` - Handle login form submission
- `togglePassword(inputId, iconElement)` - Toggle password visibility
- `loadDepartments()` - Load daftar departemen dari API

**Validations:**
- Email format validation
- Phone number (10-15 digits)
- Password minimum 8 characters
- Password confirmation match
- Department selection required

## Usage

### Register Page (`resources/views/auth/register.blade.php`)
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

<script src="{{ asset('js/auth-token-manager.js') }}"></script>
<script src="{{ asset('js/auth-form-handler.js') }}"></script>
```

### Login Page (`resources/views/auth/login.blade.php`)
```html
<form onsubmit="handleLogin(event)">
    <input type="email" id="email" required>
    <input type="password" id="password" required>
    <button type="submit" id="btnLogin">MASUK SEKARANG</button>
</form>

<script src="{{ asset('js/auth-token-manager.js') }}"></script>
<script src="{{ asset('js/auth-form-handler.js') }}"></script>
```

## Testing Flow

### Test Case 1: Register New User
1. Buka halaman register: `http://localhost:8000/register`
2. Isi form:
   - Nama: "Test User"
   - WhatsApp: "081234567890"
   - Email: "test@arwanacitra.com"
   - Departemen: (pilih salah satu)
   - Password: "password123"
   - Confirm Password: "password123"
3. Klik "DAFTAR SEKARANG"
4. Sistem harus:
   - Validasi form client-side
   - Kirim ke `/api/register` dengan method POST
   - Simpan token ke localStorage
   - Redirect ke `/dashboard/requester` (default role for new users)

### Test Case 2: Login with Email
1. Buka halaman login: `http://localhost:8000/login`
2. Isi form:
   - Email: "test@arwanacitra.com"
   - Password: "password123"
3. Klik "MASUK SEKARANG"
4. Sistem harus:
   - Validasi input
   - Kirim ke `/api/login` dengan method POST
   - Simpan token dan roles ke localStorage
   - Redirect ke dashboard sesuai role:
     - requester → `/dashboard/requester`
     - technician → `/dashboard/technician`
     - helpdesk → `/dashboard/helpdesk`
     - supervisor → `/dashboard/supervisor`
     - master_admin → `/dashboard/admin`

### Test Case 3: Login with Phone
1. Di halaman login, gunakan nomor WhatsApp sebagai "login" field
2. Sistem harus:
   - Detect bahwa input adalah phone (bukan email)
   - Kirim phone sebagai login field ke API
   - Proses sama seperti email login

### Test Case 4: Validation Errors
1. **Empty Fields**: Jika ada field kosong, tampilkan error "Field harus diisi"
2. **Invalid Email**: Jika email format salah, tampilkan "Email tidak valid"
3. **Invalid Phone**: Jika phone bukan angka 10-15 digit, tampilkan error
4. **Password Mismatch**: Jika password != password_confirmation, tampilkan error
5. **Short Password**: Jika password < 8 karakter, tampilkan error

### Test Case 5: API Errors
1. Jika email sudah terdaftar:
   - API return: `{"message": "Email already exists"}`
   - Frontend tampilkan: "Email atau nomor WhatsApp sudah terdaftar"
2. Jika login gagal:
   - API return: `{"message": "Invalid credentials"}`
   - Frontend tampilkan: "Email atau password salah"

### Test Case 6: Token Storage
Buka browser DevTools → Application → LocalStorage:
- `auth_token` - Contains Sanctum token
- `auth_user` - Contains user object JSON
- `auth_roles` - Contains roles array JSON
- `auth_permissions` - Contains permissions array JSON

### Test Case 7: Protected Routes
1. Logout (clear token)
2. Coba akses `/dashboard`
3. Seharusnya redirect ke `/login` atau tampilkan error

## Troubleshooting

### Issue: "API_URL is not defined"
**Solution**: Pastikan `custom-auth.blade.php` layout sudah include script yang set `API_URL`:
```php
<script>
    const API_URL = ("{{ config('app.url') }}".trim() || window.location.origin).replace(/\/$/, '');
</script>
```

### Issue: Token tidak tersimpan
**Solution**: Check browser console untuk error. Pastikan:
1. Token string tidak kosong
2. localStorage tersedia (tidak disabled)
3. No CORS errors saat fetch API

### Issue: Department tidak load
**Solution**: Pastikan endpoint `/api/departments` sudah aktif dan return format:
```json
{
  "data": [
    {"id": 1, "name": "IT"},
    {"id": 2, "name": "HR"}
  ]
}
```

### Issue: Redirect ke dashboard gagal
**Solution**: Pastikan:
1. Role dari API valid (master_admin, helpdesk, supervisor, technician, requester)
2. Route dashboard untuk role tersebut sudah ada
3. TokenManager.getDashboardUrl() return URL yang benar

## API Endpoints

### POST `/api/register`
```
Request:
{
  "name": "User Name",
  "email": "user@example.com",
  "phone": "081234567890",
  "department_id": 1,
  "password": "password123",
  "password_confirmation": "password123"
}

Response (201):
{
  "message": "Register success",
  "user": {...},
  "token": "token_string",
  "roles": ["requester"],
  "permissions": [...]
}

Error (422):
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

### POST `/api/login`
```
Request:
{
  "login": "user@example.com or 081234567890",
  "password": "password123"
}

Response (200):
{
  "message": "Login success",
  "user": {...},
  "token": "token_string",
  "roles": ["technician"],
  "permissions": [...]
}

Error (401):
{
  "message": "Invalid credentials"
}
```

## Security Notes

1. **Token Storage**: Token disimpan di localStorage (XSS risk). Pertimbangkan untuk:
   - Implement CSP headers
   - Use httpOnly cookies (but requires backend adjustment)
   - Regular token refresh

2. **Frontend Validation**: Frontend validation hanya untuk UX, backend HARUS validate ulang

3. **API Communication**: Semua API calls sudah menggunakan HTTPS di production

4. **CORS**: Pastikan CORS middleware di Laravel sudah properly configured

## Future Improvements

1. **Token Refresh**: Implement automatic token refresh sebelum expired
2. **Remember Me**: Add "Remember me" checkbox untuk persistent login
3. **Social Login**: Integrate dengan Google/Facebook untuk SSO
4. **Two Factor Auth**: Add 2FA untuk security tambahan
5. **Session Management**: Implement logout dari semua device

