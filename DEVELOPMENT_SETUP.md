# Setup Development Environment - Ticketing System Arwana

## 🚀 Initial Setup (Hanya dilakukan sekali)

### 1. Install PHP Dependencies
```bash
composer install
```

### 2. Install Node Dependencies
```bash
npm install
```

### 3. Setup Environment
```bash
# Copy .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Setup database (migration & seed)
php artisan migrate
php artisan db:seed

# (Optional) Setup master admin
php artisan db:seed --class=InitialMasterAdminSeeder
```

### 4. Build Assets
```bash
# Development build (one-time)
npm run build

# OR Development dengan hot reload (recommended)
npm run dev
```

---

## 💻 Daily Development

### Option A: Running with Hot Reload (RECOMMENDED)

**Terminal 1 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 2 - Laravel Server:**
```bash
php artisan serve
```

Akses: `http://localhost:8000`

**Keuntungan:**
- Asset changes auto-reload
- CSS/JS changes terlihat langsung
- Lebih cepat saat development

---

### Option B: Without Hot Reload

**Single Terminal:**
```bash
php artisan serve
```

**Jika ada perubahan CSS/JS:**
```bash
npm run build
```

---

## 📋 Commands Reference

### Vite/NPM Commands
```bash
# Development with hot reload
npm run dev

# Production build (dijalankan setelah changes ke CSS/JS)
npm run build

# Check vulnerability
npm audit

# Fix vulnerable packages
npm audit fix
```

### Laravel Commands
```bash
# Run development server
php artisan serve

# Database
php artisan migrate
php artisan migrate:fresh  # Reset database
php artisan db:seed
php artisan db:seed --class=RolePermissionSeeder

# Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Tinker (Laravel REPL)
php artisan tinker
```

---

## 🔧 Common Issues

### Error: Vite manifest not found

**Cause:** Assets belum di-build

**Solution:**
```bash
npm run build
# atau
npm run dev
```

---

### Error: npm command not found

**Cause:** Node.js tidak ter-install

**Solution:** 
1. Download & install dari https://nodejs.org
2. Restart terminal/IDE
3. Run `npm install` lagi

---

### Error: composer command not found

**Cause:** Composer tidak ter-install atau tidak di-PATH

**Solution:**
1. Install Composer dari https://getcomposer.org
2. Atau gunakan `php composer.phar` jika file composer.phar ada

---

### Error: Database connection error

**Cause:** Database belum di-setup atau .env salah

**Solution:**
1. Periksa .env file
2. Setup database: `php artisan migrate`
3. Run seed: `php artisan db:seed`

---

## ✅ Quick Checklist

- [ ] Composer installed
- [ ] Node.js installed
- [ ] `composer install` dijalankan
- [ ] `npm install` dijalankan
- [ ] `.env` file dikonfigurasi
- [ ] `php artisan key:generate` dijalankan
- [ ] Database migrations: `php artisan migrate`
- [ ] Database seeding: `php artisan db:seed`
- [ ] Assets built: `npm run build` atau `npm run dev`

---

## 🎯 Testing API

Gunakan Postman Collection:
- File: `Ticketing_System_Arwana_Complete_API.postman_collection.json`
- Dokumentasi: [POSTMAN_COLLECTION_GUIDE.md](POSTMAN_COLLECTION_GUIDE.md)

---

## 📚 Dokumentasi

- [README.md](README.md) - Project overview
- [USER_MANAGEMENT_IMPLEMENTATION.md](USER_MANAGEMENT_IMPLEMENTATION.md) - User Management Setup
- [USER_MANAGEMENT_API.md](USER_MANAGEMENT_API.md) - User Management API Detail
- [POSTMAN_COLLECTION_GUIDE.md](POSTMAN_COLLECTION_GUIDE.md) - Postman Testing Guide

---

**Last Updated:** January 28, 2026
