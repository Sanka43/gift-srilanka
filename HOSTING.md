# Hosting on giftsrilanka.com

This app is configured to be host-ready for **https://www.giftsrilanka.com**. For **step-by-step cPanel deployment**, see **[DEPLOY-CPANEL.md](DEPLOY-CPANEL.md)**.

## 1. Server / DNS

- Point **giftsrilanka.com** and **www.giftsrilanka.com** to your server (A/CNAME as needed).
- Ensure the web server document root is the **`public`** folder of this project (e.g. in cPanel set document root to `public_html/yourfolder/public`).

## 2. Environment (.env) on production

Set at least:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.giftsrilanka.com
SESSION_DOMAIN=.giftsrilanka.com
SESSION_SECURE_COOKIE=true
```

- Generate `APP_KEY`: `php artisan key:generate`
- Set production `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (e.g. cPanel MySQL: `giftcbtm_giftsrilanka`).
- Configure `MAIL_*` and `MAIL_FROM_ADDRESS` (e.g. `noreply@giftsrilanka.com`).

See `.env.example` for a production block and **DEPLOY-CPANEL.md** for the full `.env` template.

## 3. Trusted hosts

Trusted hosts in `bootstrap/app.php` allow both **giftsrilanka.com** and **www.giftsrilanka.com** when `APP_URL` is set to either. No extra config needed.

## 4. After deploy

- `composer install --no-dev --optimize-autoloader`
- `php artisan key:generate` (if `APP_KEY` is empty)
- `php artisan config:cache` / `route:cache` / `view:cache`
- `npm ci && npm run build` (or upload a pre-built `public/build/`)
- Permissions: `storage` and `bootstrap/cache` writable (e.g. `775`)
- `php artisan storage:link`
