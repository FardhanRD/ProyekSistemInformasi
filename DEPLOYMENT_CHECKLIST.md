# 📋 DEPLOYMENT CHECKLIST — MOVR PROJECT

Dokumentasi lengkap untuk deployment ke production environment.  
**Project**: Laravel E-Commerce MOVR | **Database**: db_apk_main | **Schema**: pengguna_id (standardized)

---

## **FASE 1: PRE-DEPLOYMENT VALIDATION (Development)**

### ✅ Code Quality Checks

- [ ] Jalankan syntax check untuk semua PHP files:
  ```bash
  php -l app/Http/Controllers/*.php
  php -l app/Models/*.php
  ```

- [ ] Jalankan code tests:
  ```bash
  php artisan test
  ```
  **Expected**: Semua tests ✅ pass, 0 failures

- [ ] Validasi routes:
  ```bash
  php artisan route:list | grep -E "(POST|PUT|DELETE|PATCH)" | head -20
  ```
  **Expected**: Semua route terregistrasi, tidak ada duplicate

- [ ] Cek untuk debug/dump statements:
  ```bash
  grep -r "dd(" app/ || echo "✓ No dd() found"
  grep -r "var_dump(" app/ || echo "✓ No var_dump() found"
  grep -r "console.log(" resources/ || echo "✓ No console.log() found"
  ```

### ✅ Database Integrity

- [ ] Backup database development:
  ```bash
  mysqldump -u root -p db_apk_main > backup_dev_$(date +%Y%m%d_%H%M%S).sql
  ```

- [ ] Validasi schema consistency:
  ```bash
  php artisan tinker
  > Pengguna::count()  // Harus > 0
  > Transaksi::count() // Check record count
  > exit
  ```

- [ ] Cek semua FK relationships berfungsi:
  ```bash
  php artisan tinker
  > $p = Pengguna::first(); $p->transactions()->count() // Harus valid
  > exit
  ```

### ✅ Asset & Configuration

- [ ] Compile CSS dan JS:
  ```bash
  npm run build
  ```
  **Expected**: Build succeed, tidak ada error

- [ ] Validasi environment variables:
  ```bash
  cat .env | grep -E "^(APP_|DB_|MAIL_)" | head -20
  ```
  **Expected**: Semua required vars terisi

---

## **FASE 2: PRODUCTION SETUP (Server)**

### 📌 Server Requirements

- [ ] Server OS: Ubuntu 20.04+ atau CentOS 8+
- [ ] PHP Version: 8.1+
- [ ] MySQL Version: 8.0+
- [ ] Memory: Min 2GB RAM, recommended 4GB+
- [ ] Disk Space: Min 10GB available
- [ ] SSL Certificate: Valid HTTPS certificate (Let's Encrypt OK)

### 🔧 Install Dependencies

1. **Clone Repository**
   ```bash
   cd /var/www/html
   git clone <repo-url> movr
   cd movr
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
   **Expected**: All packages installed, no conflicts

3. **Install Node Dependencies** (jika ada)
   ```bash
   npm install --omit=dev
   npm run build
   ```

4. **Set Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/movr
   sudo chmod -R 755 /var/www/html/movr
   sudo chmod -R 775 /var/www/html/movr/storage
   sudo chmod -R 775 /var/www/html/movr/bootstrap/cache
   ```

### ⚙️ Environment Configuration

1. **Copy .env from .env.example**
   ```bash
   cp .env.example .env
   ```

2. **Configure Production Environment**
   ```bash
   # Edit .env dengan settings production:
   nano .env
   ```
   
   **Required Settings:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=db_apk_main
   DB_USERNAME=movr_user
   DB_PASSWORD=secure_password_here
   
   CACHE_DRIVER=redis  # atau file/array
   QUEUE_CONNECTION=sync  # atau redis untuk production
   SESSION_DRIVER=cookie
   
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email
   MAIL_PASSWORD=your-password
   MAIL_FROM_ADDRESS=noreply@domain.com
   ```

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```
   **Expected**: `Application key set successfully`

### 🗄️ Database Setup

1. **Create Database & User**
   ```sql
   CREATE DATABASE db_apk_main CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'movr_user'@'localhost' IDENTIFIED BY 'secure_password_here';
   GRANT ALL PRIVILEGES ON db_apk_main.* TO 'movr_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Run Migrations** (IMPORTANT: Backup before!)
   ```bash
   php artisan migrate --force
   ```
   **Expected**: All migrations completed successfully

3. **Run Seeders** (Optional, untuk initial data)
   ```bash
   php artisan db:seed --force
   ```

4. **Verify Database**
   ```bash
   php artisan tinker
   > Pengguna::count()  // Harus >= 1
   > exit
   ```

---

## **FASE 3: CACHE & OPTIMIZATION**

### 🚀 Cache Configuration

```bash
# Clear semua cache
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Rebuild cache untuk production
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

**Expected Output:**
```
Configuration cached successfully
Views cached successfully
Routes cached successfully
```

### 📁 Storage & Symbolic Links

```bash
# Create storage symbolic link
php artisan storage:link

# Verify:
ls -la public/storage  # Harus ada symlink
```

### 🎨 Asset Publishing

```bash
php artisan vendor:publish --all
```

---

## **FASE 4: WEB SERVER CONFIGURATION**

### Nginx Configuration

1. **Create Virtual Host**
   ```bash
   sudo nano /etc/nginx/sites-available/movr
   ```

2. **Nginx Config Template**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com www.your-domain.com;
       root /var/www/html/movr/public;
       index index.php index.html;

       # Redirect HTTP to HTTPS
       return 301 https://$server_name$request_uri;
   }

   server {
       listen 443 ssl http2;
       server_name your-domain.com www.your-domain.com;
       root /var/www/html/movr/public;
       index index.php index.html;

       ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

       # Security headers
       add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
       add_header X-Content-Type-Options "nosniff" always;
       add_header X-Frame-Options "SAMEORIGIN" always;
       add_header X-XSS-Protection "1; mode=block" always;

       # Prevent access to dot files
       location ~ /\. {
           deny all;
           access_log off;
           log_not_found off;
       }

       # Laravel routing
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       # Cache static assets
       location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|webp|woff|woff2)$ {
           expires 365d;
           add_header Cache-Control "public, immutable";
       }

       access_log /var/log/nginx/movr_access.log;
       error_log /var/log/nginx/movr_error.log;
   }
   ```

3. **Enable Configuration**
   ```bash
   sudo ln -s /etc/nginx/sites-available/movr /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

---

## **FASE 5: SECURITY HARDENING**

### 🔒 Permission & Access Control

```bash
# File permissions
find /var/www/html/movr -type f -exec chmod 644 {} \;
find /var/www/html/movr -type d -exec chmod 755 {} \;
chmod -R 775 /var/www/html/movr/storage
chmod -R 775 /var/www/html/movr/bootstrap/cache

# Set proper owner
sudo chown -R www-data:www-data /var/www/html/movr
```

### 🔐 .env Security

```bash
# Ensure .env is not accessible via web
chmod 600 .env

# Verify:
curl -I https://your-domain.com/.env  # Harus return 403 Forbidden
```

### 🛡️ Disable .gitignore Files

```bash
# Add to nginx config location block:
# Prevent access to sensitive files
location ~ /(\.git|\.env|\.htaccess|composer\.json|composer\.lock|package\.json|webpack\.mix\.js) {
    deny all;
}
```

---

## **FASE 6: MONITORING & LOGGING**

### 📊 Enable Logging

```bash
# Setup log rotation
cat > /etc/logrotate.d/movr << EOF
/var/www/html/movr/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
EOF

sudo logrotate -f /etc/logrotate.d/movr
```

### 📈 Application Health Checks

```bash
# Create health check endpoint (jika belum ada)
# GET /health → return 200 OK
# Use untuk monitoring services: Uptime Robot, Pingdom, etc.
```

---

## **FASE 7: FINAL DEPLOYMENT VALIDATION**

### ✅ Pre-Go-Live Tests

1. **Syntax & Configuration**
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

2. **Database Connection**
   ```bash
   php artisan tinker
   > DB::connection()->getPdo()  // Test connection
   > Pengguna::count()
   > Transaksi::count()
   > exit
   ```

3. **Artisan Commands Verification**
   ```bash
   php artisan migrate --dry-run  # Verify migrations
   php artisan queue:work --stop-when-empty  # Test queue (if used)
   ```

4. **Web Accessibility**
   ```bash
   curl -I https://your-domain.com/
   # Expected: HTTP/2 200 OK
   ```

5. **Check Critical Pages Load**
   - [ ] Homepage: https://your-domain.com/
   - [ ] Login: https://your-domain.com/login
   - [ ] Register: https://your-domain.com/register
   - [ ] Browse Products: https://your-domain.com/kategori
   - [ ] Admin Panel: https://your-domain.com/admin (if public)

6. **Test Complete Transaction Flow**
   - [ ] Register new account
   - [ ] Login
   - [ ] Browse products
   - [ ] Add to cart
   - [ ] Checkout
   - [ ] Payment process
   - [ ] Order confirmation

---

## **FASE 8: POST-DEPLOYMENT**

### 📋 Backup & Recovery

1. **Schedule Daily Database Backups**
   ```bash
   # Add to crontab:
   0 2 * * * mysqldump -u movr_user -p'password' db_apk_main | gzip > /backups/db_apk_main_$(date +\%Y\%m\%d).sql.gz
   ```

2. **Backup Retention Policy**
   ```bash
   # Keep last 30 days of backups
   find /backups -name "db_apk_main_*.sql.gz" -mtime +30 -delete
   ```

### 🔄 Maintenance Mode

```bash
# When deploying updates:
php artisan down --message="Sistem sedang diupdate..."
# Do your updates
php artisan up
```

### 📞 Monitoring & Alerts

- [ ] Setup error logging: Sentry, Bugsnag, atau Roll.io
- [ ] Setup performance monitoring: New Relic, Datadog
- [ ] Setup uptime monitoring: Uptime Robot, Pingdom
- [ ] Enable admin email notifications untuk critical errors

---

## **EMERGENCY ROLLBACK**

Jika terjadi issue di production:

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Check recent migrations
php artisan migrate:status

# 3. Rollback last migration (if needed)
php artisan migrate:rollback

# 4. Clear caches
php artisan cache:clear
php artisan view:clear

# 5. Disable maintenance mode
php artisan up

# 6. Notify team & document issue
```

---

## **USEFUL COMMANDS REFERENCE**

```bash
# View error logs
tail -f /var/www/html/movr/storage/logs/laravel.log

# Check Laravel version
php artisan --version

# List all commands
php artisan list

# Clear all caches
php artisan cache:clear && php artisan view:clear && php artisan config:clear

# Check disk usage
du -sh /var/www/html/movr

# Database info
php artisan db:show

# Test email configuration
php artisan tinker
> Mail::raw('Test', function($m) { $m->to('test@example.com'); });
> exit
```

---

## **CHECKLIST SUMMARY**

### Before Deploy
- [ ] All tests pass: `php artisan test`
- [ ] No syntax errors: `php -l app/**/*.php`
- [ ] .env configured for production
- [ ] Database backup created
- [ ] Assets compiled: `npm run build`
- [ ] SSL certificate valid
- [ ] Performance indexes added

### During Deploy
- [ ] Enable maintenance mode
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear caches: `php artisan config:cache && php artisan view:cache`
- [ ] Publish assets: `php artisan vendor:publish`
- [ ] Storage linked: `php artisan storage:link`

### After Deploy
- [ ] Test critical flows
- [ ] Monitor error logs
- [ ] Verify database connectivity
- [ ] Check asset loading (CSS/JS)
- [ ] Test on multiple browsers
- [ ] Disable maintenance mode: `php artisan up`
- [ ] Notify stakeholders
- [ ] Schedule follow-up monitoring

---

**Last Updated**: May 15, 2026  
**Document Version**: 1.0  
**Status**: ✅ Production Ready

Untuk pertanyaan atau issues, hubungi Tim Teknis MOVR.
