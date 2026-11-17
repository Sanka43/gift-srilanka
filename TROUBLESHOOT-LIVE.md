# Live site (giftsrilanka.com) – දත්ත පෙන්වන්නේ නැති විට (Data not showing)

ලයිව් සයිට් එකේ database එකේ තියෙන data (categories, products) පෙන්වන්නේ නැති නම් පහත පියවර පරීක්ෂා කරන්න.

---

## 0. Product/Category images 404 (අයිකන් 404 – Failed to load resource)

Browser console එකේ `nau9usHQ1SIumm7kGfQ4BPlBB5MHw6iAcOGpRLZF0T.jpg` වගේ filename එකට **404** එනවා නම්, ඒ product/category **images** host එකේ හරියට serve වෙන්නේ නැහැ.

**කාරණා දෙක:**

1. **Storage link නැහැ** — `public/storage` එක `storage/app/public` එකට link කරලා නැහැ.
2. **Image files upload කරලා නැහැ** — Database එක import කරලා paths (උදා: `products/xxxxx.jpg`) තියෙනවා, නමුත් server එකේ `storage/app/public/products/` යට **files** copy කරලා නැහැ.

**කරන්න (පළමු විකල්පය – symlink හදන්න):**

| පියවර | කළ යුතු දෙය |
|--------|----------------|
| A | Host එකේ project root එකෙන් run කරන්න: `php artisan storage:link` |
| B | **"The [public/storage] link already exists"** එනවා නම්: ඒක **symlink එක නෙමෙයි, folder එක** වෙන්න පුළුවන්. cPanel File Manager හෝ SSH එකෙන් `public/storage` එක **delete** කරලා, නැවත `php artisan storage:link` run කරන්න. (Symlink support නැති host එකක් නම් පහත "විකල්පය 2" බලන්න.) |
| C | `public/storage` එක symlink එක හරිද බලන්න: `ls -la public/storage` (එය `storage/app/public` කියලා යොමු වෙන්න ඕනේ) |
| D | පැරණි data තියෙනවා නම්: Local එකේ `storage/app/public/products/` සහ `storage/app/public/categories/` යට තියෙන files host එකේ එම path එකට upload කරන්න. |

**විකල්පය 2 – Symlink හැකියාව නැති host එකේ (images තවම load වෙන්නේ නැති නම්):**

Admin එකෙන් product add කරද්දී image save වෙනවා, නමුත් site එකේ පෙන්වන්නේ නැති නම්, host එකේ `public/storage` එක **symlink එක විදිහට හැදෙන්නේ නැහැ** (folder එක විදිහට තියෙනවා). එහෙම නම් **symlink අවශ්‍ය නැති විදිහට** uploads direct `public/storage` යට යවන්න පුළුවන්:

1. Host එකේ `.env` එක open කරලා මේ line එක **add** කරන්න (හෝ uncomment කරන්න):  
   `FILESYSTEM_PUBLIC_ROOT=public`
2. Config cache clear කරන්න:  
   `php artisan config:clear`  
   `php artisan config:cache`
3. මේ සිට **නව uploads** (admin product/category images) `public/storage/products/` සහ `public/storage/categories/` යට direct save වෙනවා — ඒ නිසා images load වෙන්න ඕනේ.
4. **පැරණි products** (database එකේ තියෙන, නමුත් files තවම `storage/app/public/` යට තියෙනවා): ඒ files **copy** කරලා host එකේ `public/storage/products/` (හෝ categories) යට යවන්න. හෝ admin එකෙන් product edit කරලා image නැවත upload කරන්න.

විස්තර: **කොටස 6** (පහත) බලන්න.

---

## 1. Production server එකේ `.env` පරීක්ෂා කරන්න

සර්වරයේ project root එකේ (උදා: `public_html/giftsrilanka/`) `.env` file එක තියෙනවාද බලන්න. එහි **අනිවාර්යයෙන්** මේ values හරිද කියලා බලන්න:

```env
# Database – MySQL use කරන්න (sqlite නෙමෙයි)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=giftcbtm_giftsrilanka
DB_USERNAME=giftcbtm_giftsrilanka
DB_PASSWORD=ඔයාගේ_cPanel_db_password
```

- **ගැටලුව:** `DB_CONNECTION=sqlite` තියෙනවා නම් local default එක use වෙලා තියෙන්න පුළුවන්. Production එකේ **MySQL** use කරන්න: `DB_CONNECTION=mysql`.
- **ගැටලුව:** `DB_PASSWORD` හිස් නම් හෝ වැරදි නම් database connect වෙන්නේ නැහැ.

`.env` හරි කරලා save කරලා, config cache clear කරන්න:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 2. Database එකේ data තියෙනවාද බලන්න

SSH හෝ cPanel Terminal use කරලා project root එකෙන් run කරන්න:

```bash
php artisan app:check-db
```

මේක database connection හොඳද, categories / products count කීයද කියලා පෙන්වයි. Connection fail නම් `.env` එකේ `DB_*` values හරි කරන්න.

**Count 0 නම්:** Database එක හිස්. Schema + data import කරන්න:

- cPanel **phpMyAdmin** එකෙන් `giftcbtm_giftsrilanka` database එකට ඔයාගේ `.sql` dump එක import කරන්න, **හෝ**
- Migrations + seeders use කරනවා නම්:  
  `php artisan migrate --force`  
  `php artisan db:seed --force`

---

## 3. Cache clear කරන්න

පැරණි (හිස්) data cache වෙලා තියෙන්න පුළුවන්. Home page categories/featured/trending cache එකත් clear කරන්න:

```bash
php artisan app:check-db --clear-cache
```

හෝ අතින්:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

(Home page cache keys: `home_featured_products`, `home_trending_products`, `home_categories` — ඒවා `app:check-db --clear-cache` run කරොත් clear වෙනවා.)

---

## 4. Checklist (සාරාංශය)

| පියවර | කළ යුතු දෙය |
|--------|----------------|
| 1 | Production `.env` එකේ `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` හරියිද බලන්න |
| 2 | `php artisan app:check-db` run කරලා connection OK + counts හරිද බලන්න |
| 3 | Count 0 නම් SQL import හෝ `migrate` + `db:seed` run කරන්න |
| 4 | `php artisan app:check-db --clear-cache` හෝ `config:clear` + `cache:clear` + `config:cache` run කරන්න |
| 5 | Browser එකෙන් https://giftsrilanka.com refresh කරන්න |

---

## 5. තවත් ගැටළු

- **500 error:** `storage/logs/laravel.log` බලන්න; `storage` සහ `bootstrap/cache` writable ද බලන්න.
- **DB connection error in log:** cPanel MySQL හි database name, username, password හරිද බලන්න; බොහෝ වෙලාවට `DB_HOST=localhost` use වේ.

---

## 6. Category / product images 404 (`/storage/...` 404)

Browser console එකේ `nau9usHQ1SIumm7kGfQ4BPlBB5MHw6iAcOGpRLZF0T.jpg` වගේ **404 (Failed to load resource)** එනවා නම්, ඒ product image එක. Full URL එක `https://giftsrilanka.com/storage/products/xxxxx.jpg` විදිහට. Image files හෝ storage link එක production එකේ හරියට නැහැ.

### 6.1 Storage symlink තියෙනවාද බලන්න

Production server එකේ (project root එකෙන්) run කරන්න:

```bash
php artisan storage:link
```

මේක `public/storage` එක `storage/app/public` එකට link කරනවා. Link එක නැත්තං `/storage/...` requests 404 දෙනවා.

- **"The [public/storage] link already exists"** එනවා නම්: ඒක **සාමාන්‍ය folder එක** වෙලා තියෙන්න පුළුවන් (symlink එක නෙමෙයි). එහෙම නම් images load වෙන්නේ නැහැ. **විකල්ප:** (1) ඒ folder එක delete කරලා නැවත `php artisan storage:link` run කරන්න (symlinks allow කරන host එකක් නම්), හෝ (2) **කොටස 0** එකේ **විකල්පය 2** පරිදි `.env` එකේ `FILESYSTEM_PUBLIC_ROOT=public` set කරන්න — එතකොට symlink අවශ්‍ය නැතිව uploads direct `public/storage` යට යනවා.
- cPanel **File Manager** එකෙන් බලන්න: `public/storage` එක **symlink** ද **folder** ද. Terminal එකෙන්: `ls -la public/storage` — symlink නම් `storage/app/public` කියලා යොමු වෙන්න ඕනේ.

### 6.2 Image files server එකේ තියෙනවාද බලන්න

- **Product images:** DB එකේ `product_images.image_path` (උදා: `products/nau9usHQ1SIumm7kGfQ4BPlBB5MHw6iAcOGpRLZF0T.jpg`) තියෙනවා නම්, ඒ **file server එකේ** `storage/app/public/products/` යට තියෙන්න ඕනේ.
- **Category images:** `categories.image_path` (උදා: `categories/5KqnMrh0YI8II59GsCHISPDMoxZIEfM9Ix6YJLPt.jpg`) තියෙනවා නම්, ඒ file `storage/app/public/categories/` යට තියෙන්න ඕනේ.

Database එක විතරක් import කරලා (SQL dump) products add කරලා තියෙනවා නම්, **image files** automatically යන්නේ නැහැ. Local එකේ තියෙන files production එකට යවන්න ඕනේ:

- Local: `storage/app/public/products/` සහ `storage/app/public/categories/` folders **යට තියෙන සියලු files** FTP හෝ cPanel File Manager use කරලා host එකේ එම path එකටම upload කරන්න.
- හෝ production admin එකෙන් product/category එක edit කරලා image නැවත upload කරන්න.

### 6.3 Summary (images 404)

| පියවර | කළ යුතු දෙය |
|--------|----------------|
| 1 | Server එකේ `php artisan storage:link` run කරලා `public/storage` → `storage/app/public` link එක හැකියාව තියෙනවාද බලන්න |
| 2 | `storage/app/public/categories/` (සහ products තියෙනවා නම් `storage/app/public/products/`) යට image files upload කරලා තියෙනවාද බලන්න |
| 3 | ගැටළුව තවම තියෙනවා නම් browser එකෙන් direct URL එක open කරලා 404 එන path එක හරියට server path එකට match වෙනවාද බලන්න |

වැඩිදුර: [DEPLOY-CPANEL.md](DEPLOY-CPANEL.md), [HOSTING.md](HOSTING.md).
