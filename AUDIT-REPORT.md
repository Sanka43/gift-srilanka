# Gifts Sri Lanka – Project Audit Report (2026-11-25)

**Stack:** Laravel 11.48 · PHP 8.2 · MySQL · Blade + Tailwind/Alpine · PayHere · cPanel (GitHub Actions rsync deploy)
**Audit scope:** routes, auth, checkout/order/stock, PayHere payment flow, admin panel, chat, uploads, config, deploy pipeline, tests, git hygiene.
**Audit එකේ සීමා:** app එක run කළේ නැහැ; `composer audit` / `php artisan test` run කළේ නැහැ (මේ machine එකේ `composer` PATH එකේ නෑ); හැම Blade view / JS file එකක්ම line-by-line බැලුවේ නෑ; git history එකේ තියෙන පරණ `.sql` dump එකේ content බැලුවේ නෑ.

---

## 1. සාරාංශය

Project එක සාමාන්‍යයෙන් **හොඳින් ගොඩනඟලා** තියෙනවා (Service layer, FormRequests, row-lock stock handling, ownership checks, rate limits, security headers). නමුත් **live යන්න කලින් / දැන්ම හදන්න ඕනේ** ගැටළු කිහිපයක් තියෙනවා – විශේෂයෙන් **PayHere payment flow**, **deploy pipeline** (chat files මැකෙන්න පුළුවන්), සහ **git history එකේ DB dump එක**.

| Severity | ගණන |
|---|---|
| 🔴 High | 5 |
| 🟠 Medium | 9 |
| 🟡 Low | 9 |

---

## 2. 🔴 High

### H1. PayHere secret හිස් නම් IPN forge කරන්න පුළුවන්
`app/Services/PayHereService.php` – `merchantSecret` හිස් වුනත් `md5('')` වලින් signature හදලා verify කරනවා. `.env.example` එකේ `PAYHERE_MERCHANT_SECRET=` හිස්. merchant_id එක checkout form එකෙන් public. Production `.env` එකේ secret දාන්න අමතක වුනොත්, ඕනෑම කෙනෙක්ට `/payment/payhere/notify` එකට forged request එකක් යවලා ඕනෑම order එකක් "paid" කරන්න පුළුවන්.
**Fix:** `merchantId` හෝ `merchantSecret` හිස් නම් `verifyNotification()` false දෙන්න / `generatePaymentData()` exception දාන්න.

### H2. Failed/Cancelled IPN එකකින් "paid" order එක "failed" වෙන්න පුළුවන්
`PaymentController::notify()` – `STATUS_FAILED / CANCELED` branch එකේදී order එක දැනටමත් `paid` ද කියලා check කරන්නේ නෑ. Customer එකම order එකට දෙවෙනි attempt එකක් cancel කළොත් (PayHere එකම order_id එකට IPN කිහිපයක් යවනවා) paid order එක `failed` වෙනවා.
**Fix:** `payment_status === 'paid'` නම් failed/cancel IPN ignore කරන්න.

### H3. IPN idempotency / race condition නෑ
`markAsPaid()` call කරන්න කලින් `payment_status !== 'paid'` check එක transaction/lock එකකින් පිටත. PayHere IPN retry/duplicate එන විට order confirmation email දෙපාරක් යනවා, status history duplicate වෙනවා.
**Fix:** `DB::transaction` ඇතුළේ `Order::lockForUpdate()->find()` කරලා check කරන්න; email එක transaction commit වුනාට පස්සේ, එක පාරක් විතරක්.

### H4. Deploy එකේදී chat attachments (private files) මැකෙනවා
`.github/workflows/deploy.yml` – `rsync --delete` use කරනවා, නමුත් exclude කරලා තියෙන්නේ `/storage/app/public` සහ `/storage/logs` විතරයි. `storage/app/chat-attachments/**` (private disk) සහ `storage/framework/sessions|cache` repo එකේ නැති නිසා **හැම push එකකදීම server එකෙන් මැකෙනවා**.
**Fix:** `--exclude="/storage"` (හෝ `/storage/app` + `/storage/framework`) එකතු කරන්න, නැත්නම් `--delete` ඉවත් කරන්න.

### H5. DB dump එක git history එකේ ඉන්නවා
`database/giftcbtm_giftsrilanka.sql` file එක initial commit (`badf3b6`) එකේ commit වෙලා තියෙනවා; දැන් staged delete එකක් විතරයි. History එකේ තවමත් තියෙනවා. Repo එක GitHub එකේ (CI එක GitHub Actions) – private ද කියලා confirm කරන්න. ඒ dump එකේ customer data / password hashes තියෙන්න පුළුවන්. `database/database.sqlite` (192KB) එකත් track වෙලා.
**Fix:** repo private බව තහවුරු කරන්න; `git filter-repo` වලින් history එකෙන් ඉවත් කරන්න; dump එකේ තිබුණු user ලාගේ passwords reset කරන්න; `database/database.sqlite` untrack කරන්න (`.gitignore` එකට දාන්න).

---

## 3. 🟠 Medium

| # | ගැටළුව | විස්තරය / Fix |
|---|---|---|
| M1 | **Sandbox auto-pay on return** | `PaymentController::return()` – `APP_ENV=local` + `PAYHERE_SANDBOX=true` නම් payment නැතුව order "paid" වෙනවා. දැන් `.env` / `.env.example` default `APP_ENV=local`, `APP_DEBUG=true`. Production server `.env` එකේ එය නැවත set නොකළොත් **නොමිලේ orders**. Fix: `.env.example` default `production`/`false` කරන්න; මේ shortcut එක සම්පූර්ණයෙන් ඉවත් කරන්න (sandbox IPN test කරන්න ngrok use කරන්න). |
| M2 | **Signature log වෙනවා** | IPN mismatch වෙද්දී `expected` md5sig එක log එකට ලියනවා – log එක කියවන්න පුළුවන් කෙනෙක්ට valid signature එක ලැබෙනවා. Fix: `expected` log කරන්න එපා. |
| M3 | **Card data log/store වෙනවා** | සම්පූර්ණ IPN payload (`card_holder_name`, masked `card_no`) `Log::info` එකට සහ `payment_meta` එකට යනවා. PII minimise කරන්න; ඕනේ fields විතරක් තියාගන්න. |
| M4 | **`OrderService::createOrder` dead code** | `return DB::transaction(...)` එකට පස්සේ `cacheService->invalidateProductRelated()` + `return $order` කිසිදා run වෙන්නේ නෑ → order එකට පස්සේ stock/product cache refresh වෙන්නේ නෑ. Fix: `$order = DB::transaction(...)` කරන්න. |
| M5 | **Unpaid online orders stock hold කරනවා** | Order එක හදනකොටම stock අඩු වෙනවා; payment නොකරපු/ failed online orders auto-cancel වෙන්නේ නෑ. Scheduled job එකකින් (උදා: 30–60 min) `pending` + unpaid online orders cancel කරලා stock restore කරන්න. |
| M6 | **Paid order cancel/refund flow නෑ** | Customer ට paid online order එකක් cancel request කරන්න පුළුවන්; admin approve කළාම stock restore වෙනවා, නමුත් `payment_status` / refund track වෙන්නේ නෑ. `refunded` status එකක් + PayHere refund process එකක් අවශ්‍යයි. |
| M7 | **Laravel 11 / PHP 8.2 / Node 18 support ඉවරයි/ඉවර වෙනවා** | Laravel 11 security support 2026-02 වලින් ඉවරයි; PHP 8.2 security support 2026-12-31; Node 18 EOL. Laravel 12+ (PHP 8.3+, Node 20/22) වලට upgrade plan කරන්න. `composer audit` + `npm audit` run කරන්න. |
| M8 | **CSP දුර්වලයි** | `script-src` හි `'unsafe-inline' 'unsafe-eval'`, `style-src https:`. HSTS header නෑ. XSS protection ඇත්තටම අඩුයි. Nonce-based CSP + `Strict-Transport-Security` එකතු කරන්න (`X-XSS-Protection` deprecated – ඉවත් කළාට කමක් නෑ). |
| M9 | **Deploy එකේ safety අඩුයි** | Tests නැතුව `main` push එකටම deploy; `php artisan migrate --force` DB backup නැතුව; `StrictHostKeyChecking=no` (MITM). Fix: tests job එකක් + backup step + known_hosts pin. |

---

## 4. 🟡 Low

1. **Admin CSV export injection** – `full_name` `=cmd|...` වගේ අගයක් Excel formula වෙනවා. `= + - @` වලින් පටන් ගන්නවා නම් `'` prefix කරන්න.
2. **Chat attachment signed URL expire වෙන්නේ නෑ** (`signedRoute(..., null, false)`) – leak වුනොත් ස්ථිරවම access. `temporarySignedRoute` (උදා: 1h) use කරන්න; `Content-Disposition` filename escape කරන්න.
3. **Admin UI labels වැරදියි** – `cash_on_delivery` orders "Bank Transfer" ලෙස (payments page + CSV, `bankPaid` metric). COD ≠ bank transfer; method එකක් ලෙස `bank_transfer` වෙන් කරන්න.
4. **Status inconsistency** – `UpdateOrderRequest` payment_status `pending,paid,cod` allow කරනවා; admin PaymentController `paid,pending,failed`. Admin ට cancelled order එකක්වත් "paid" කරන්න පුළුවන්; හැම "paid" click එකටම email යනවා. State-transition rules එකතු කරන්න.
5. **Order status transitions validate වෙන්නේ නෑ** (`delivered → pending` වගේ).
6. **FormRequest `authorize()` = true** admin requests වල – route middleware මත විතරක් රඳා පවතී (defense-in-depth).
7. **Navbar composer** හැම page request එකකම cart queries කිහිපයක් run කරනවා (cart count + items + products). Cache/request-level memoize කරන්න.
8. **`QUEUE_CONNECTION=sync`** – "queue" කරන emails checkout request එක ඇතුළේම යනවා (SMTP slow නම් checkout slow). Production එකේ queue worker/cron එකක් සකසන්න.
9. **Structured-data `{!! json_encode() !!}`** `JSON_HEX_TAG` නැතුව – product name එකේ `</script>` තිබුණොත් (admin input) break වෙනවා.

අමතරව: `ProfileController::destroy` user delete කරනකොට avatar file එක මැකෙන්නේ නෑ, orders වලට user_id orphan වෙන්න පුළුවන් (soft delete / anonymize කරන්න); `HOST-READY-CPANEL.md` වැනි docs 7ක් duplicate content සහිතයි – එකකට merge කරන්න; `.env.example` එකේ සැබෑ bank account number එක තියෙනවා (repo public නම් ඉවත් කරන්න).

---

## 5. ✅ හොඳ දේවල්

- Password policy ශක්තිමත් (8+, mixed case, number, symbol); login throttle (5/min + RateLimiter); login වලදී `session()->regenerate()`.
- `role_id` mass-assignment **fix කරලා** (`User::$fillable` වලින් ඉවත් කළා – දැනට uncommitted).
- IPN signature `hash_equals` සමඟ; amount + merchant_id check; CSRF exempt එක IPN route එකට විතරයි.
- Order/payment pages හැම එකකම ownership check (IDOR නෑ); chat conversation access `customer_id` check.
- Checkout වලදී `lockForUpdate` stock handling; server-side price calculation (client price trust නෑ); checkout_token double-submit guard.
- Upload validation (mimes/size, chat `mimetypes` server-detected); Blade `{{ }}` escaping; raw SQL parameterised (`whereRaw` bindings).
- Security headers (CSP, X-Frame-Options, nosniff, Referrer-Policy); `.htaccess` sensitive-file rules; production error page masking.
- Proper migrations + performance indexes; Service-layer architecture.

---

## 6. Tests & Quality

- Tests තියෙන්නේ **3 files** විතරයි (PayHere unit 4 tests, Chat feature 2 files). **Checkout, stock, order cancel, IPN flow, admin authorization** සඳහා tests නෑ – payment code එකට අවම වශයෙන් IPN tests (valid / invalid sig / amount mismatch / duplicate / failed-after-paid) ලියන්න.
- `storage/logs/laravel.log` හි `Command "test" is not defined` – dev dependencies (collision) install වෙලා නෑ වගේ; `composer install` (with dev) run කරන්න.
- Uncommitted changes 11 files (security hardening වගේ පෙනෙනවා – `.htaccess`, `User`, `OrderService`, `CartService` …). Review කරලා commit කරන්න.

---

## 7. අනුපිළිවෙලින් කළ යුතු දේ (Action plan)

1. **(දැන්ම)** Repo private බව check කරන්න → history එකෙන් `.sql` ඉවත් කරන්න → DB/user passwords rotate කරන්න. (H5)
2. Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `PAYHERE_MERCHANT_SECRET` set, `PAYHERE_SANDBOX=false` තහවුරු කරන්න. (H1, M1)
3. `PaymentController::notify` සහ `PayHereService` fix: empty-secret reject, paid → failed block, lock + idempotency, log cleanup. (H1–H3, M2, M3)
4. `deploy.yml`: `/storage` exclude කරන්න; tests step; backup; host key pin. (H4, M9)
5. `OrderService` dead-code fix; unpaid-order auto-cancel job. (M4, M5)
6. Refund/cancel flow + admin payment-status rules. (M6, Low 3–5)
7. Laravel 12 / PHP 8.3 upgrade plan; `composer audit`, `npm audit`; CSP + HSTS tighten. (M7, M8)
8. Payment/checkout tests ලියන්න.

මේවායින් ඕනෑම එකක් (විශේෂයෙන් 3 සහ 4) මට code එකෙන්ම fix කරලා දෙන්න පුළුවන්.
