# Gifts Sri Lanka – Single Vendor Gift E-Commerce

Laravel 11 application with Blade, Tailwind CSS, and Alpine.js. Single vendor only (no multi-vendor). Built for future expansion (e.g. a separate Real Estate module).

## Requirements

- PHP 8.2+
- Composer 2
- Node.js 18+ (for Vite)
- MySQL 8 or MariaDB

## Setup

1. **Install PHP dependencies** (fix SSL if needed, e.g. `composer config -g -- disable-tls false` or use a certificate bundle):
   ```bash
   composer install
   ```

2. **Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env`: set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

3. **Database**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   Seeding only creates roles (admin, customer). Create your first admin user via registration, then assign the admin role in the database if needed.

4. **Storage link** (for product images):
   ```bash
   php artisan storage:link
   ```

5. **Frontend assets**:
   ```bash
   npm install
   npm run build
   ```
   For development: `npm run dev`

6. **Run the app**:
   ```bash
   php artisan serve
   ```
   Open http://127.0.0.1:8000

## Host / Production: Removing old seed data

If you replaced the `database/seeders` folder (e.g. to remove hardcoded products/users), the **database on the host still contains the old rows**. Seeders only run when you execute `php artisan db:seed`; they do not delete existing data.

To remove the old seeded/demo products and categories on the host, run once:

```bash
php artisan app:clear-seed-data --force
```

This deletes products with slugs `teddy-bear`, `greeting-card`, `chocolate-box`, `xmas-tree-decor`, any product whose name starts with `demo`, and the categories `birthday`, `anniversary`, `christmas` (only if they have no products left). It also clears the homepage cache. Optional: `--all-products` or `--all-categories` to remove everything in that table (empty categories only for `--all-categories`). Then:

```bash
php artisan cache:clear
```

After that, the homepage will show only real data you add via the admin.

## Optional: Laravel Breeze

The project includes a custom auth implementation compatible with the described behaviour. To switch to official Breeze (Blade stack):

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```
Then re-apply role assignment in `RegisteredUserController` (set `role_id` to customer) and keep the `EnsureUserHasRole` middleware and admin routes.

## Structure

- **Routes**: `routes/web.php` (shop + auth), `routes/admin.php` (admin panel), `routes/auth.php` (login, register, password reset).
- **Admin**: All under `/admin`, protected by `auth` + `role:admin` middleware.
- **Cart**: Session-based; see `App\Services\CartService` and `App\Services\OrderService`.
- **Future modules**: Place new modules under `app/Modules/` (see `app/Modules/README.md`). Do not add multi-vendor logic to this codebase.

## Features

- **Auth**: Register (customer role), login, password reset. Admin and Customer roles.
- **Shop**: Home, Shop (category filter), Product detail, Cart, Checkout (guest allowed), Order confirmation.
- **Admin**: Dashboard (orders count, revenue, low stock), Categories CRUD, Products CRUD (multiple images), Orders list and status update.
- **Payment**: Cash on Delivery and Online (structure only; no gateway yet).

## Code quality

- Form Request validation for checkout, categories, products.
- Resource controllers for admin categories and products.
- `OrderService` for order creation and total calculation; controllers stay thin.

## Production Readiness Notes

- `.env` must never be committed. If it is currently tracked, run `git rm --cached .env` and rotate any exposed credentials.
- Use `.env.production.example` as the base server environment file.
- Required production values:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `SESSION_SECURE_COOKIE=true`
  - `QUEUE_CONNECTION=database`
  - `FILESYSTEM_DISK=public`
- Mail should use SMTP in production (`MAIL_MAILER=smtp`) with matching `MAIL_HOST`, `MAIL_PORT`, and `MAIL_ENCRYPTION` from your provider.
- Queue tables are included in migrations (`jobs`, `job_batches`, `failed_jobs`), so run migrations before enabling queue workers.

## Final Deploy Commands

Run from project root on the live server:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
