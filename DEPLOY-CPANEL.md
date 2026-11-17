# Deploy Gifts Sri Lanka to cPanel (https://www.giftsrilanka.com)

This guide covers deploying the Laravel app to cPanel with the document root at `public/` and using the production database.

---

## 1. cPanel setup

### Domain

- **Domain:** https://www.giftsrilanka.com (and giftsrilanka.com → redirect to www)
- In cPanel **Domains** or **Addon Domains**, ensure the domain’s **Document Root** points to the `public` folder of this project, e.g.:
  - `public_html` → **not** used for Laravel root  
  - Use: `public_html/giftsrilanka/public` **or** set document root to `public_html/public` if you upload the whole project under `public_html`.

**Recommended:** Put the full project in e.g. `public_html/giftsrilanka` and set the **domain’s document root** to `public_html/giftsrilanka/public`. That way `app/`, `config/`, `.env` stay above the web root.

### SSL

- In cPanel **SSL/TLS** (or **Let’s Encrypt**), install a certificate for `giftsrilanka.com` and `www.giftsrilanka.com`.
- Enable **Force HTTPS** so HTTP redirects to HTTPS.

---

## 2. Database (MySQL in cPanel)

- Create a MySQL database, e.g. `giftcbtm_giftsrilanka`.
- Create a user (e.g. `giftcbtm_giftsrilanka`) with a strong password and **All privileges** on that database.
- Note: **DB host** is usually `localhost` on cPanel (or the hostname cPanel shows for “Remote MySQL”).

Import your schema and data:

- **phpMyAdmin:** Import your `.sql` dump into `giftcbtm_giftsrilanka`.
- **Or** SSH: `mysql -u giftcbtm_giftsrilanka -p giftcbtm_giftsrilanka < backup.sql`
- **Or** run migrations: `php artisan migrate --force` (only if the DB is empty and you use migrations).

---

## 3. Upload project

- Upload the whole project (excluding `node_modules/`, `vendor/` if you prefer to install on server) into e.g. `public_html/giftsrilanka/`.
- Ensure **document root** for the site is `public_html/giftsrilanka/public` (see step 1).

If your cPanel plan cannot point the domain to `.../public`, move only the contents of `public/` into `public_html/` and update `index.php` paths:

```php
require __DIR__.'/../giftsrilanka/vendor/autoload.php';
$app = require_once __DIR__.'/../giftsrilanka/bootstrap/app.php';
```

Adjust `../giftsrilanka` to your real project-root path.

---

## 4. Create `.env` on the server

In the project root (e.g. `public_html/giftsrilanka/`), create `.env`:

- **Option A:** `cp .env.production.example .env` then set `DB_PASSWORD` and run `php artisan key:generate`
- **Option B:** `cp .env.example .env` then edit as below

Edit `.env` and set at least:

```env
APP_NAME="Gifts Sri Lanka"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Colombo
APP_URL=https://www.giftsrilanka.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=giftcbtm_giftsrilanka
DB_USERNAME=giftcbtm_giftsrilanka
DB_PASSWORD=YOUR_CPANEL_DB_PASSWORD

SESSION_DOMAIN=.giftsrilanka.com
SESSION_SECURE_COOKIE=true

# Logging (required for storage/logs/laravel.log to be written on the host)
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

# Optional: mail (use cPanel Email or SMTP)
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@giftsrilanka.com
MAIL_FROM_NAME="${APP_NAME}"
```

- Replace `YOUR_CPANEL_DB_PASSWORD` with the actual database password.
- Generate `APP_KEY`: run `php artisan key:generate` (see step 5).

---

## 5. Run commands (SSH or cPanel Terminal)

From the project root (e.g. `~/public_html/giftsrilanka` or `~/giftsrilanka`):

```bash
# Install PHP dependencies (production, no dev)
composer install --no-dev --optimize-autoloader

# Generate application key if .env has empty APP_KEY
php artisan key:generate

# Migrate including queue tables
php artisan migrate --force

# Link storage for uploaded files
php artisan storage:link

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets (if you have Node on server)
npm ci
npm run build
```

If you don’t have Node on the server, build locally and upload the `public/build/` folder (and keep `public/hot` deleted on production).

Queue worker (recommended):

```bash
php artisan queue:work --queue=default --tries=3 --timeout=90
```

If worker is temporarily down, the app still runs and mail queue failures are logged with fallback send attempts.

---

## 6. Permissions

Ensure the web server can write to Laravel’s cache and storage:

```bash
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache
chmod 775 storage/logs
chown -R your_cpanel_user:your_cpanel_user storage bootstrap/cache
```

Use the user/group cPanel uses for PHP (often your account username). If `storage/logs/laravel.log` is not updating on the host, ensure `.env` has `LOG_CHANNEL=stack`, `LOG_STACK=single`, and `LOG_LEVEL=warning`, then run `php artisan config:clear` and `php artisan config:cache`.

---

## 7. Storage link

Create the public storage symlink for uploads (e.g. product images):

```bash
php artisan storage:link
```

---

## 8. Optional: Scheduler (cron)

For `php artisan schedule:run` (if you use scheduled tasks), in cPanel **Cron Jobs** add:

```text
* * * * * cd /home/YOUR_CPANEL_USER/public_html/giftsrilanka && php artisan schedule:run >> /dev/null 2>&1
```

Replace `YOUR_CPANEL_USER` and path with your actual path to the project root.

---

## 9. Checklist

- [ ] Domain document root = `.../public`
- [ ] SSL installed and Force HTTPS on
- [ ] Database created, user assigned, schema/data imported
- [ ] `.env` created with production values and real `DB_PASSWORD`
- [ ] `APP_KEY` generated
- [ ] `composer install --no-dev --optimize-autoloader` run
- [ ] `php artisan config:cache` (and route/view cache) run
- [ ] `public/build/` present (built locally or with `npm run build`)
- [ ] `storage` and `bootstrap/cache` writable
- [ ] `php artisan storage:link` run
- [ ] `.env` not publicly reachable and not committed to git
- [ ] Visit https://www.giftsrilanka.com and test login, cart, checkout

---

## 10. Troubleshooting

- **500 error:** Check `storage/logs/laravel.log`; ensure `storage` and `bootstrap/cache` are writable and `APP_DEBUG=false` in production.
- **DB connection:** Confirm `DB_HOST=localhost` (or the host cPanel shows), correct database name, user, and password.
- **Mixed content / cookies:** Ensure `APP_URL=https://www.giftsrilanka.com`, `SESSION_SECURE_COOKIE=true`, and `SESSION_DOMAIN=.giftsrilanka.com`.
- **Assets 404:** Ensure `npm run build` was run and `public/build/` is uploaded; do not use `npm run dev` in production.

For more context see **HOSTING.md**.
