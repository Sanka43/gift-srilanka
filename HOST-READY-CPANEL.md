# Gifts Sri Lanka – cPanel හි Host කරන හැටි (Step by Step)

මෙම ගයිඩ් එක පියවරෙන් පියවර follow කරලා ඔබේ Laravel project එක cPanel hosting එකේ deploy කරන්න පුළුවන්.

---

## ඔබට අවශ්‍ය දේවල් (පළමුව තියෙන්න ඕනේ)

- cPanel hosting account එක (domain එක link කරලා)
- Domain එක (උදා: giftsrilanka.com) – cPanel එකට point වෙලා තියෙන්න ඕනේ
- Local computer එකේ project එක (Laravel) හොඳින් run වෙනවා නම් හොඳයි
- Database backup එක (local එකේ data තියෙනවා නම් export කරලා `.sql` file එක)

---

## STEP 1: Local එකේ Build කරන්න (Upload කරන්න කලින්)

### 1.1 Frontend assets build කරන්න

Project folder එක open කරලා terminal/command prompt එක run කරන්න:

```bash
npm run build
```

මෙතනින් `public/build/` folder එක create වෙනවා (CSS, JS files). මේ folder එක server එකට upload කරන්න ඕනේ.

### 1.2 Upload කරන්න එපා කරන දේවල්

Server එකට upload **කරන්න එපා**:

- `node_modules/` (මේක බරයි, server එකේ අවශ්‍ය නැහැ)
- `.env` (server එකේ අලුත් `.env` එක හදා ගන්නවා)
- `.git/` (optional – skip කරන්න පුළුවන්)
- `storage/logs/*.log` (optional)
- `public/hot` (dev only – තියෙනවා නම් delete කරන්න)

**Upload කරන්න ඕනේ:** ඉතිරි හැම දෙයක්ම – `app/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `vendor/` (හෝ server එකේ `composer install` කරන්න ඕනේ නම් vendor skip කරන්න පුළුවන්), `composer.json`, `artisan`, etc.

---

## STEP 2: cPanel එකට Login වෙන්න

1. Hosting provider එක ඔබට දුන් **cPanel login URL** එක open කරන්න (උදා: `https://yourdomain.com:2083` හෝ `https://cpanel.yourhost.com`)
2. **Username** සහ **Password** දාලා login වෙන්න.

---

## STEP 3: Domain එකේ Document Root Set කරන්න

1. cPanel එකේ **Domains** (හෝ **Addon Domains**) click කරන්න.
2. ඔබේ domain එක (උදා: `giftsrilanka.com` හෝ `www.giftsrilanka.com`) select කරන්න.
3. **Document Root** යන තැන තියෙන path එක මතක තියා ගන්න.  
   සාමාන්‍යයෙන් තියෙන්නේ `public_html` හෝ `public_html/giftsrilanka` වගේ.
4. **මැදිහත්ව:** ඔබ පියවර 4 එකේ project එක upload කරලා ඉවරයි කියලා අනිවාර්යයෙන්ම **පියවර 4 ඉවර වුණාට පස්සේ** මෙතැන ආපසු එලියේ **Document Root** එක **project එකේ `public` folder එකට** set කරන්න.  
   උදාහරණ:  
   - Project එක upload කරලා තියෙන්නේ `public_html/giftsrilanka/` යට නම්, Document Root එක:  
     `public_html/giftsrilanka/public`  
   - Project එකම `public_html` යට දාලා තියෙනවා නම්, Document Root:  
     `public_html/public`

**මතක තියා ගන්න:** Document root එක **හැම වෙලාවෙම** `public` folder එක වෙන්න ඕනේ. එහෙම නැත්නම් site එක 500 error දානවා හෝ අනාරක්ෂිත වෙනවා.

---

## STEP 4: MySQL Database හදා ගන්න

### 4.1 Database create කරන්න

1. cPanel එකේ **MySQL® Databases** (හෝ **MySQL Databases**) click කරන්න.
2. **Create New Database** යට:
   - **Database Name:** උදා: `giftcbtm_giftsrilanka` (හෝ ඔබට ඕනේ name එක – cPanel එක ඉදිරියෙන් prefix එකක් දානවා)
   - **Create Database** click කරන්න.
3. Create වුණ database එකේ **full name** එක copy කරලා තියා ගන්න (උදා: `cpaneluser_giftcbtm_giftsrilanka`).

### 4.2 Database User හදා ගන්න

1. **MySQL Users** කොටසේ **Add New User** click කරන්න.
2. **Username** එකක් දාන්න (උදා: `giftcbtm_giftsrilanka` – cPanel එක prefix දානවා).
3. **Password** එක හදන්න (Strong password එකක්) – **Generate** use කරලා හදා ගන්න පුළුවන්.
4. **Create User** click කරන්න.
5. **Full username** එක සහ **password** එක copy කරලා safe තැනක save කරන්න (`.env` එකට පස්සේ අවශ්‍යයි).

### 4.3 User එක Database එකට Link කරන්න

1. **Add User To Database** කොටසේ:
   - **User** එක select කරන්න (ඔබ හදපු user එක).
   - **Database** එක select කරන්න (ඔබ හදපු database එක).
   - **Add** click කරන්න.
2. **Privileges** පිටුවේ **ALL PRIVILEGES** tick කරන්න (හෝ "All" select කරන්න).
3. **Make Changes** click කරන්න.

### 4.4 Database එකට Data Import කරන්න

1. cPanel එකේ **phpMyAdmin** open කරන්න.
2. වම් පැත්තේ ඔබ හදපු **database** එක click කරන්න.
3. **Import** tab එක click කරන්න.
4. **Choose File** click කරලා ඔබේ `.sql` backup file එක select කරන්න (local එකේ export කරපු එක).
5. **Go** (හෝ **Import**) click කරන්න. Success message එකක් එනතුරු බලා ගන්න.

**නැත්නම්:** Server එකේ SSH/Terminal තියෙනවා නම්, empty database එකක් හදා ගෙන `php artisan migrate --force` run කරන්න පුළුවන් (local එකේ data අවශ්‍ය නැත්නම්).

---

## STEP 5: Project Files Upload කරන්න

### 5.1 File Manager හෝ FTP use කරන්න

**Option A – cPanel File Manager:**

1. cPanel එකේ **File Manager** click කරන්න.
2. `public_html` folder එකට යන්න. අවශ්‍ය නම් **New Folder** click කරලා `giftsrilanka` නම් folder එක හදන්න.
3. `giftsrilanka` (හෝ ඔබ තෝරපු folder) ඇතුළට යන්න.
4. **Upload** click කරන්න.
5. Local එකේ project folder එකේ තියෙන files හා folders (STEP 1 එකේ කිව්වා විදිහට exclude කරලා) select කරලා upload කරන්න.  
   (Folder structure එක තියෙන්න: `app`, `config`, `database`, `public`, `resources`, `routes`, `vendor` නැත්නම් composer install කරන්න ඕනේ, `composer.json`, `composer.lock`, `artisan`, etc.)

**Option B – FTP (FileZilla වගේ):**

1. cPanel එකේ **FTP Accounts** එකෙන් හෝ hosting එකේ දුන් FTP details use කරන්න.
2. Connect වෙලා `public_html/giftsrilanka/` (හෝ ඔබ තෝරපු path) යට ඉහත එකම structure එක upload කරන්න.

### 5.2 Document Root එක නැවත Set කරන්න

Upload ඉවර උනාට පස්සේ **STEP 3** එකට ආපසු යන්න:

- **Domains** → ඔබේ domain → **Document Root** = `public_html/giftsrilanka/public` (path එක ඔබේ folder name අනුව වෙනස් වෙන්න පුළුවන්).

---

## STEP 6: Server එකේ `.env` File එක හදා ගන්න

### 6.1 `.env` create කරන්න

1. cPanel **File Manager** එකේ project **root** folder එකට යන්න (උදා: `public_html/giftsrilanka`).
2. තියෙනවා නම් `.env.production.example` හෝ `.env.example` file එක **Copy** කරන්න.
3. Copy එකේ name එක `.env` කියලා rename කරන්න (හෝ New File → name: `.env`).

### 6.2 `.env` එක Edit කරන්න

`.env` file එක **Edit** කරන්න. අවම වශයෙන් මේ values තියෙන්න ඕනේ (domain, DB name, user, password ඔබේ ඒවාට අනුව වෙනස් කරන්න):

```env
APP_NAME="Gifts Sri Lanka"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Colombo
APP_URL=https://www.giftsrilanka.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
DELIVERY_CHARGE=350

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=giftcbtm_giftsrilanka
DB_USERNAME=giftcbtm_giftsrilanka
DB_PASSWORD=ඔබේ_ඇත්ත_ database_ password_ එක

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=.giftsrilanka.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@giftsrilanka.com
MAIL_FROM_NAME="${APP_NAME}"

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning
```

- **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD** යනාදිය ඔබ STEP 4 එකේ හදපු database හා user අනුව දාන්න (cPanel full names: උදා: `cpaneluser_giftcbtm_giftsrilanka`).
- **LOG_CHANNEL**, **LOG_STACK**, **LOG_LEVEL** – මේ තියෙන්න ඕනේ `storage/logs/laravel.log` host එකේ update වෙන්න. නැත්තං errors log file එකට යන්නේ නැහැ.
- **APP_URL** ඔබේ domain එකට set කරන්න (උදා: `https://www.giftsrilanka.com`).
- **SESSION_DOMAIN** ඔබේ domain එකට set කරන්න (උදා: `.giftsrilanka.com` – dot එක තියෙන්න ඕනේ).
- Mail යැවීමට cPanel Email හෝ SMTP use කරන්නවා නම් **MAIL_MAILER**, **MAIL_HOST**, **MAIL_PORT**, **MAIL_USERNAME**, **MAIL_PASSWORD**, **MAIL_ENCRYPTION** ආදිය cPanel එකේ mail settings අනුව set කරන්න.

Save කරන්න.

---

## STEP 7: cPanel Terminal හෝ SSH use කරලා Commands Run කරන්න

1. cPanel එකේ **Terminal** (හෝ **SSH Access**) open කරන්න.
2. Project **root** folder එකට යන්න:

```bash
cd ~/public_html/giftsrilanka
```

(Path එක ඔබේ එකට අනුව වෙනස් කරන්න – `~/public_html` හෝ `~/giftsrilanka` වගේ.)

### 7.1 Composer dependencies install කරන්න

```bash
composer install --no-dev --optimize-autoloader
```

(Server එකේ `vendor` upload කරලා නැත්තං මේක අවශ්‍යයි.)

### 7.2 Application key generate කරන්න

```bash
php artisan key:generate
```

(.env එකේ APP_KEY එක automatically fill වෙනවා.)

### 7.3 Config, routes, views cache කරන්න

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7.4 Storage link එක හදා ගන්න (product images ආදියට)

```bash
php artisan storage:link
```

### 7.5 (Optional) Node නැත්තං local එකේ build කරලා තියෙනවා නම්

Server එකේ Node/npm නැත්තං local එකේ `npm run build` කරලා තියෙන `public/build/` folder එක upload කරලා තියෙනවා නම් මේක skip කරන්න. Server එකේ Node තියෙනවා නම්:

```bash
npm ci
npm run build
```

---

## STEP 8: Permissions Set කරන්න

Terminal එකේම (project root එකේ):

```bash
chmod -R 775 storage bootstrap/cache
```

`storage/logs` folder එක තියෙන්න ඕනේ හා web server ට write කරන්න පුළුවන් වෙන්න ඕනේ (log file එක update වෙන්න):

```bash
mkdir -p storage/logs
chmod -R 775 storage
chmod 775 storage/logs
```

cPanel එකේ account username එක අනුව (උදා: `cpaneluser`) owner set කරන්න අවශ්‍ය නම්:

```bash
chown -R cpaneluser:cpaneluser storage bootstrap/cache
```

(`cpaneluser` යන තැන ඔබේ cPanel username එක දාන්න.)

---

## STEP 9: SSL (HTTPS) Enable කරන්න

1. cPanel එකේ **SSL/TLS Status** හෝ **Let's Encrypt™ SSL** (හෝ **SSL/TLS**) open කරන්න.
2. ඔබේ domain එකට certificate එක **Install** කරන්න (උදා: `giftsrilanka.com`, `www.giftsrilanka.com`).
3. **Force HTTPS Redirect** (හෝ **Always Use HTTPS**) enable කරන්න. එහෙම කරන්නේ නැත්තං **.htaccess** එකෙන් තියෙන redirect එක use වෙනවා (project එකේ `public/.htaccess` එකේ තියෙනවා).

---

## STEP 10: Final Check – Site එක Test කරන්න

1. Browser එකේ ඔබේ domain එක open කරන්න: `https://www.giftsrilanka.com` (ඔබේ URL එක).
2. Home page එක load වෙනවාද බලන්න.
3. **Register** කරන්න (නව user එකක්).
4. Email එකට verification link එක යනවාද බලන්න (mail config හරි නම්).
5. **Login** කරන්න (verify කළ ඉන්පසු).
6. **Shop**, **Cart**, **Checkout** (test order එකක්) වගේ පියවර test කරන්න.

---

## Checklist (හැම පියවරක්ම හරියට කළාද බලන්න)

- [ ] Local එකේ `npm run build` කරලා `public/build/` තියෙනවා
- [ ] cPanel එකේ Document Root = `.../public` (project එකේ `public` folder)
- [ ] MySQL database හා user හදා ගෙන, user එක database එකට All Privileges දාලා තියෙනවා
- [ ] Database එකට data import කරලා තියෙනවා (phpMyAdmin හෝ migrate)
- [ ] Project files හරියට upload කරලා තියෙනවා (node_modules, .env exclude)
- [ ] Server එකේ `.env` හදා ගෙන APP_URL, DB_*, SESSION_DOMAIN, SESSION_SECURE_COOKIE හරියට set කරලා තියෙනවා
- [ ] `composer install --no-dev --optimize-autoloader` run කරලා තියෙනවා
- [ ] `php artisan key:generate` run කරලා තියෙනවා
- [ ] `php artisan config:cache` / `route:cache` / `view:cache` run කරලා තියෙනවා
- [ ] `php artisan storage:link` run කරලා තියෙනවා
- [ ] `storage` හා `bootstrap/cache` permissions 775 හා (අවශ්‍ය නම්) owner set කරලා තියෙනවා
- [ ] SSL install කරලා Force HTTPS on කරලා තියෙනවා
- [ ] Browser එකෙන් register, login, cart, checkout test කරලා තියෙනවා

---

## ගැටළු තියෙනවා නම් (Troubleshooting)

- **500 error:**  
  - `storage/logs/laravel.log` file එක open කරලා error message එක බලන්න.  
  - `storage` හා `bootstrap/cache` folders වලට write permission තියෙනවාද බලන්න (775).  
  - Production එකේ `.env` එකේ `APP_DEBUG=false` තියෙනවාද බලන්න.

- **Database connection error:**  
  - `.env` එකේ `DB_HOST=localhost`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` cPanel එකේ full names අනුව හරියට තියෙනවාද බලන්න.

- **Login / session / cookie ගැටළු:**  
  - `APP_URL` ඔබේ exact URL එකට set කරලා තියෙනවාද (උදා: `https://www.giftsrilanka.com`).  
  - `SESSION_DOMAIN=.giftsrilanka.com`, `SESSION_SECURE_COOKIE=true` set කරලා තියෙනවාද බලන්න.

- **CSS/JS load නොවෙනවා (assets 404):**  
  - `public/build/` folder එක server එකේ තියෙනවාද බලන්න (local එකේ `npm run build` කරලා upload කරලා හෝ server එකේ `npm run build` කරලා).  
  - Production එකේ `npm run dev` use කරන්න එපා.

- **Verification email යන්නේ නැහැ:**  
  - `.env` එකේ MAIL_* settings (SMTP හෝ cPanel mail) හරියට set කරලා තියෙනවාද බලන්න.  
  - cPanel **Email** section එකෙන් test email එකක් යවලා බලන්න.

- **`storage/logs/laravel.log` host එකේ update නොවෙනවා:**  
  - Server එකේ `.env` එකේ මේ lines තියෙනවාද බලන්න: `LOG_CHANNEL=stack`, `LOG_STACK=single`, `LOG_LEVEL=warning` (හෝ `error` / `debug`). නැත්තං ඒවා add කරන්න.  
  - Config cache clear කරලා නැවත cache කරන්න (`.env` change කළාට පස්සේ):  
    `php artisan config:clear`  
    `php artisan config:cache`  
  - `storage` හා `storage/logs` folders වලට web server ට write permission තියෙනවාද බලන්න:  
    `chmod -R 775 storage`  
    `chmod 775 storage/logs`  
  - `storage/logs` folder එක තියෙනවාද බලන්න. නැත්තං:  
    `mkdir -p storage/logs`  
    `chmod 775 storage/logs`  
  - Owner හරියට තියෙනවාද බලන්න (cPanel user):  
    `chown -R ඔබේ_cpanel_user:ඔබේ_cpanel_user storage bootstrap/cache`

---

මෙම ගයිඩ් එක follow කරලා ඔබේ Gifts Sri Lanka site එක cPanel එකේ හරියට host වෙනවා නම්, register, login (real email verification එක්ක), cart, checkout හැම දෙයක්ම work වෙන්න ඕනේ.
