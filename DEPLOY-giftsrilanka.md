# Deploy to giftsrilanka.com (cPanel / shared host)

## 1. Build locally (already done)

```bash
npm run build
```

This created `public/build/` with hashed CSS and JS. **Upload the whole project including `public/build/`** to the host.

---

## 2. What to upload

Upload everything **except**:

- `node_modules/` (no need on server)
- `.env` (create a **new** `.env` on the server with production values)
- `.git/` (optional to skip)
- `storage/logs/*.log` (optional)
- `public/hot` (dev only; delete if present)

---

## 3. `.env` on the server

Create or edit `.env` on the host with at least:

```env
APP_NAME="Gifts Sri Lanka"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Colombo
APP_URL=https://www.giftsrilanka.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=giftcbtm_giftsrilanka
DB_USERNAME=giftcbtm_giftsrilanka
DB_PASSWORD=your_actual_database_password

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=.giftsrilanka.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Optional: mail (for verification emails, contact form)
MAIL_MAILER=smtp
MAIL_HOST=mail.giftsrilanka.com
MAIL_PORT=587
MAIL_USERNAME=noreply@giftsrilanka.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@giftsrilanka.com"
MAIL_FROM_NAME="${APP_NAME}"

DELIVERY_CHARGE=350
```

- Replace `DB_PASSWORD` with the real cPanel MySQL password.
- If you use SMTP for mail, set `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION=tls`.

---

## 4. On the server (SSH or cPanel Terminal)

Run once after upload:

```bash
cd /path/to/your/app   # e.g. public_html or giftsrilanka

# Install PHP dependencies (no dev packages)
composer install --no-dev --optimize-autoloader

# Generate app key if .env has empty APP_KEY
# php artisan key:generate

# Cache config and routes (optional, faster)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# (Optional) Seed roles and admin user
php artisan db:seed --class=RoleSeeder --force
```

---

## 5. Permissions

Ensure the web server can write to:

- `storage/` (recursive)
- `bootstrap/cache/`

On Linux/cPanel:

```bash
chmod -R 775 storage bootstrap/cache
# If needed, set owner to the web server user
```

---

## 6. Document root

Point the domain to the **`public`** folder:

- **Document root**: `public` (so the URL is `https://www.giftsrilanka.com/` and not `/public/`).
- In cPanel: **Domains** → your domain → **Document Root** → set to `public` or the full path ending in `public`.

---

## 7. After deploy

- Visit https://www.giftsrilanka.com and test: home, shop, register, login, cart.
- If 500 errors, check `storage/logs/laravel.log` on the server.
