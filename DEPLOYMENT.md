# Panduan Deployment - Ticketing System Arwana

Panduan lengkap untuk deploy aplikasi Ticketing System Arwana di server perusahaan dengan domain email perusahaan.

---

## 1. Kebutuhan Server (Minimum)

| Komponen | Spesifikasi Minimum |
|----------|---------------------|
| OS       | Ubuntu 22.04 LTS / Ubuntu 24.04 LTS (recommended) |
| CPU      | 2 Core |
| RAM      | 4 GB |
| Storage  | 20 GB SSD |
| Network  | IP Public / IP Internal perusahaan + akses domain |

---

## 2. Software & Environment yang Harus Diinstall

### A. Web Server — Nginx 1.24+

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install nginx -y
sudo systemctl enable nginx
```

### B. PHP 8.2 + Extensions

Project membutuhkan `php: ^8.1` (minimal). Disarankan menggunakan **PHP 8.2**.

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install php8.2-fpm php8.2-cli php8.2-common \
  php8.2-mysql php8.2-pgsql php8.2-sqlite3 \
  php8.2-mbstring php8.2-xml php8.2-curl \
  php8.2-zip php8.2-gd php8.2-intl \
  php8.2-bcmath php8.2-fileinfo \
  php8.2-tokenizer php8.2-dom \
  php8.2-redis -y
```

**Penjelasan tiap extension:**

| Extension    | Alasan                                                        |
|-------------|---------------------------------------------------------------|
| `pgsql`     | Database driver — project menggunakan PostgreSQL              |
| `mbstring`  | String multibyte (Laravel wajib)                              |
| `xml`/`dom` | Parsing XML (Laravel wajib)                                   |
| `curl`      | HTTP client (Guzzle)                                          |
| `zip`       | Composer & Maatwebsite Excel export                           |
| `gd`        | Image processing (PhpSpreadsheet)                             |
| `intl`      | Internationalization                                          |
| `bcmath`    | Precision math                                                |
| `fileinfo`  | File MIME detection                                           |
| `redis`     | Opsional, untuk cache/queue via Redis                         |


### C. PostgreSQL 15+ (Database)

Project dikonfigurasi menggunakan PostgreSQL (bukan MySQL).

```bash
sudo apt install postgresql postgresql-contrib -y
sudo systemctl enable postgresql
```

#### 1. Buat database, user, dan atur host/port

```bash
sudo -u postgres psql
```

```sql
CREATE USER arwana_user WITH PASSWORD 'password_kuat_anda';
CREATE DATABASE ticketing_system OWNER arwana_user;
GRANT ALL PRIVILEGES ON DATABASE ticketing_system TO arwana_user;
\q
```

#### 2. Cek/atur port dan listen address PostgreSQL

- Edit file konfigurasi PostgreSQL (biasanya di `/etc/postgresql/<versi>/main/postgresql.conf`):
    ```bash
    sudo nano /etc/postgresql/*/main/postgresql.conf
    ```
- Pastikan baris berikut:
    ```
    listen_addresses = 'localhost'
    port = 5432
    ```
- Restart PostgreSQL:
    ```bash
    sudo systemctl restart postgresql
    ```

#### 3. Cek port berjalan

```bash
sudo netstat -plnt | grep 5432
# atau
sudo ss -plnt | grep 5432
```
Output harus ada baris `127.0.0.1:5432` (artinya hanya menerima koneksi dari localhost).

#### 4. Cek koneksi database dari server

```bash
psql -U arwana_user -h 127.0.0.1 -p 5432 -d ticketing_system -c "SELECT 1;"
# Jika diminta password, masukkan yang tadi dibuat
# Output:  ?column? | 1
```

#### 5. Konfigurasi di .env

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ticketing_system
DB_USERNAME=arwana_user
DB_PASSWORD=arwana
```

> Jika database di server berbeda, ganti `DB_HOST` dengan IP/hostname server database, dan pastikan port 5432 terbuka dari aplikasi ke database.

### D. Composer 2.x (PHP Dependency Manager)

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version  # Pastikan v2.x
```

### E. Node.js 18 LTS + NPM (Build Assets)

Dibutuhkan karena project menggunakan **Vite 5** untuk compile CSS/JS.

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
node -v   # >= 18.x
npm -v    # >= 9.x
```

### F. Supervisor (Queue Worker)

Project menggunakan queue (`QUEUE_CONNECTION=database`) untuk notifikasi email.

```bash
sudo apt install supervisor -y
sudo systemctl enable supervisor
```

### G. Git

```bash
sudo apt install git -y
```

### H. Certbot — SSL/HTTPS (Opsional tapi Recommended)

```bash
sudo apt install certbot python3-certbot-nginx -y
```

---


## 2. Proses Deployment (ZIP)

### 2.1 Checklist Isi ZIP dari Developer

Pastikan file ZIP dari developer sudah berisi **semua file berikut** (tidak perlu build ulang di server):

```
WAJIB ada dalam ZIP:
├── app/
├── bootstrap/
├── config/
├── database/
├── public/ (termasuk public/build/ hasil npm run build)
├── resources/
├── routes/
├── storage/ (struktur saja, log/cache boleh kosong)
├── tests/
├── .env.production
├── .env.example
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── DEPLOYMENT.md
```

**JANGAN include:**
- node_modules/
- vendor/
- .env (development)
- public/hot
- storage/logs/*.log

### 2.3 Ekstrak ZIP di Server

```bash
sudo mkdir -p /var/www/ticketing-arwana
sudo unzip /tmp/ticketing-arwana-production.zip -d /var/www/ticketing-arwana
sudo chown -R www-data:www-data /var/www/ticketing-arwana
sudo chmod -R 775 /var/www/ticketing-arwana/storage
sudo chmod -R 775 /var/www/ticketing-arwana/bootstrap/cache
```

> **Catatan:**
> - Tidak perlu menjalankan `npm install` atau `npm run build` lagi di server jika folder `public/build/` sudah ada.
> - Semua dependensi PHP akan di-install via `composer install` (lihat langkah berikutnya).

### 2.4 Lanjutkan ke Langkah 5 (Install Dependencies di Server)

Ikuti langkah selanjutnya mulai dari **Bagian 5** di bawah (Install Dependencies di Server).

---

### 3.1 Clone & Setup Project

```bash
cd /var/www
sudo git clone <repo-url> ticketing-arwana
cd ticketing-arwana
sudo chown -R www-data:www-data /var/www/ticketing-arwana
sudo chmod -R 775 storage bootstrap/cache
```

### 3.2 Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build   # Compile assets untuk production
```

### 3.3 Konfigurasi Environment (`.env`)

```bash
cp .env.example .env
nano .env
```

Isi `.env` untuk **production**:

```dotenv
APP_NAME="Ticketing Arwana"
APP_ENV=production
APP_KEY=  # akan di-generate
APP_DEBUG=false
APP_URL=https://ticketing.arwana.com

API_BASE_URL=https://ticketing.arwana.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# ====== DATABASE ======
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ticketing_system
DB_USERNAME=arwana_user
DB_PASSWORD=password_kuat_anda

# ====== DRIVER ======
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# ====== EMAIL PERUSAHAAN (SMTP) ======
MAIL_MAILER=smtp
MAIL_HOST=mail.arwana.com          # atau smtp.arwana.com
MAIL_PORT=587                       # TLS: 587, SSL: 465
MAIL_USERNAME=ticketing@arwana.com  # email perusahaan
MAIL_PASSWORD=password_email
MAIL_ENCRYPTION=tls                 # atau ssl
MAIL_FROM_ADDRESS="ticketing@arwana.com"
MAIL_FROM_NAME="Ticketing System Arwana"

# ====== EMAIL VERIFICATION ======
EMAIL_VERIFICATION_ENABLED=true
EMAIL_VERIFICATION_FRONTEND_URL=https://ticketing.arwana.com
EMAIL_VERIFICATION_FRONTEND_PATH=/email/verify-result
EMAIL_VERIFICATION_EXPIRATION=30

# ====== TICKET NOTIFICATION ======
TICKET_NOTIFICATION_ENABLED=true
TICKET_NOTIFICATION_FRONTEND_URL=https://ticketing.arwana.com
TICKET_NOTIFICATION_TICKET_PATH=/tickets
```

### 3.4 Generate Key & Migrate

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force    # Seed roles, permissions, categories, statuses
php artisan storage:link
```

### 3.5 Optimasi Laravel untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 4. Konfigurasi Nginx (Virtual Host)

Buat file konfigurasi:

```bash
sudo nano /etc/nginx/sites-available/ticketing-arwana
```

Isi dengan:

```nginx
server {
    listen 80;
    server_name ticketing.arwana.com;   # Ganti dengan domain Anda
    root /var/www/ticketing-arwana/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    # Upload size (untuk attachment ticket)
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site & reload:

```bash
sudo ln -s /etc/nginx/sites-available/ticketing-arwana /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL (HTTPS) — Jika menggunakan domain publik:

```bash
sudo certbot --nginx -d ticketing.arwana.com
```

---

## 5. Konfigurasi Supervisor (Queue Worker)

Buat file konfigurasi:

```bash
sudo nano /etc/supervisor/conf.d/ticketing-queue.conf
```

Isi dengan:

```ini
[program:ticketing-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ticketing-arwana/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ticketing-arwana/storage/logs/queue-worker.log
stopwaitsecs=3600
```

Jalankan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ticketing-queue:*
```

---


### 6. Konfigurasi Email Perusahaan (SMTP)

#### Jika menggunakan Gmail/Google Workspace:

1. Aktifkan 2FA (Two Factor Authentication) di akun Gmail.
2. Buat App Password di https://myaccount.google.com/apppasswords (pilih "Mail" dan device bebas).
3. Masukkan App Password (bukan password biasa) ke `MAIL_PASSWORD` di file `.env`.
4. Contoh konfigurasi:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alamatgmailanda@gmail.com
MAIL_PASSWORD=AppPasswordDariGoogle
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alamatgmailanda@gmail.com
MAIL_FROM_NAME="Ticketing System Arwana"
```

Tanpa App Password, pengiriman email akan SELALU gagal (error: authentication failed).

#### Jika perusahaan menggunakan Microsoft 365 / Exchange:

```dotenv
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Jika menggunakan SMTP perusahaan:

```dotenv
MAIL_HOST=smtp.perusahaan.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=ticketing@perusahaan.com
MAIL_PASSWORD=PasswordEmail
MAIL_FROM_ADDRESS=ticketing@perusahaan.com
```

---

## 7. Cron Job (Laravel Task Scheduler)

```bash
sudo crontab -e -u www-data
```

Tambahkan baris berikut:

```cron
* * * * * cd /var/www/ticketing-arwana && php artisan schedule:run >> /dev/null 2>&1
```

---


## 20. Ringkasan Versi Software Aplikasi

| Komponen        | Versi Minimum      | Keterangan                               |
|-----------------|-------------------|------------------------------------------|
| **Ubuntu**      | 22.04 LTS         | Server OS                                |
| **Nginx**       | 1.24+             | Web server + reverse proxy               |
| **PHP**         | 8.2.x (min 8.1)   | Runtime, sesuai `composer.json`          |
| **PHP-FPM**     | 8.2               | FastCGI process manager                  |
| **PostgreSQL**  | 15+               | Database (`DB_CONNECTION=pgsql`)         |
| **Composer**    | 2.x               | PHP dependency manager                   |
| **Node.js**     | 18.x LTS          | Build frontend assets (Vite) — opsional  |
| **NPM**         | 9.x+              | Package manager JS — opsional            |
| **Supervisor**  | 4.x               | Queue worker daemon                      |
| **Certbot**     | latest            | SSL certificate (Let's Encrypt)          |
| **Laravel**     | 10.x              | PHP framework                            |
| **Sanctum**     | 3.x               | API token authentication                 |
| **Spatie**      | 6.x               | Role & permission management             |
| **Maatwebsite** | 3.x               | Excel export/import                      |
| **Git**         | 2.x               | Version control                          |

---

---

## 9. Checklist Sebelum Go-Live

- [ ] `APP_DEBUG=false` dan `APP_ENV=production`
- [ ] Database sudah di-migrate dan di-seed
- [ ] Queue worker berjalan via Supervisor
- [ ] SMTP email perusahaan sudah tested (kirim test email)
- [ ] `EMAIL_VERIFICATION_ENABLED=true`
- [ ] `TICKET_NOTIFICATION_ENABLED=true`
- [ ] Frontend URL di `.env` sudah mengarah ke domain production
- [ ] SSL/HTTPS aktif
- [ ] `storage/` dan `bootstrap/cache/` writable oleh `www-data`
- [ ] `php artisan config:cache` sudah dijalankan
- [ ] Cron job untuk scheduler sudah aktif
- [ ] Backup database terjadwal (opsional tapi recommended)

---

## 10. Troubleshooting Umum

### Permission Error (storage/logs)
```bash
sudo chown -R www-data:www-data /var/www/ticketing-arwana/storage
sudo chmod -R 775 /var/www/ticketing-arwana/storage
```

### Queue Tidak Jalan
```bash
sudo supervisorctl status ticketing-queue:*
sudo supervisorctl restart ticketing-queue:*
# Cek log:
tail -f /var/www/ticketing-arwana/storage/logs/queue-worker.log
```

### Email Tidak Terkirim
```bash
# Test kirim email lewat tinker
php artisan tinker
> Mail::raw('Test email', function ($msg) { $msg->to('test@arwana.com')->subject('Test'); });
```

### 502 Bad Gateway
```bash
# Pastikan PHP-FPM berjalan
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

### Setelah Update Code dari Git
```bash
cd /var/www/ticketing-arwana
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart ticketing-queue:*
```
