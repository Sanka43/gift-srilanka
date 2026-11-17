# Image Storage Locations in GiftsriLanka

## Overview
This document explains where images are stored on the host server for categories and products in the GiftsriLanka Laravel application.

---

## Storage Configuration

The application uses Laravel's filesystem with the `public` disk for storing uploaded images.

### Configuration File
Location: [`config/filesystems.php`](config/filesystems.php)

```php
'public' => [
    'driver' => 'local',
    'root' => env('FILESYSTEM_PUBLIC_ROOT') === 'public'
        ? public_path('storage')
        : storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

---

## Image Storage Paths on Host

### Default Storage Structure

**Base Storage Directory:**
- **Development/Local:** `storage/app/public/`
- **Production (with symlink):** `storage/app/public/` → symlinked to `public/storage/`
- **Production (without symlink):** `public/storage/` (when `FILESYSTEM_PUBLIC_ROOT=public` in `.env`)

### Category Images

**Storage Path:**
```
storage/app/public/categories/
```
or
```
public/storage/categories/
```

**Code Reference:** [`app/Http/Controllers/Admin/CategoryController.php:48`](app/Http/Controllers/Admin/CategoryController.php:48)
```php
$data['image_path'] = $request->file('image')->store('categories', 'public');
```

**Database Field:** `categories.image_path`
- Example value: `categories/abc123xyz.jpg`

**Public URL Access:**
```
https://yourdomain.com/storage/categories/abc123xyz.jpg
```

---

### Product Images

**Storage Path:**
```
storage/app/public/products/
```
or
```
public/storage/products/
```

**Code Reference:** [`app/Services/ProductService.php:98`](app/Services/ProductService.php:98)
```php
$path = $file->store('products', 'public');
$product->images()->create(['image_path' => $path]);
```

**Database Table:** `product_images`
- Field: `image_path`
- Example value: `products/xyz789abc.jpg`

**Public URL Access:**
```
https://yourdomain.com/storage/products/xyz789abc.jpg
```

---

## Complete File System Paths

### On Your Host Server (XAMPP)

Assuming your project is at: `c:/xampp/htdocs/giftsrilanka`

#### Category Images:
```
c:/xampp/htdocs/giftsrilanka/storage/app/public/categories/
```
or (if using public storage directly):
```
c:/xampp/htdocs/giftsrilanka/public/storage/categories/
```

#### Product Images:
```
c:/xampp/htdocs/giftsrilanka/storage/app/public/products/
```
or (if using public storage directly):
```
c:/xampp/htdocs/giftsrilanka/public/storage/products/
```

---

## How Images Are Accessed

### In Blade Templates

**Category Images:**
```blade
<img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}">
```
Example: [`resources/views/admin/categories/index.blade.php:34`](resources/views/admin/categories/index.blade.php:34)

**Product Images:**
```blade
<img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
```
Example: [`resources/views/products/show.blade.php:43`](resources/views/products/show.blade.php:43)

---

## Storage Symlink

Laravel requires a symbolic link from `public/storage` to `storage/app/public` for public access to uploaded files.

### Create Symlink

**Command:**
```bash
php artisan storage:link
```

**What it does:**
Creates a symbolic link:
```
public/storage → storage/app/public
```

### For cPanel/Shared Hosting

If you cannot create symlinks, set this in your `.env` file:
```env
FILESYSTEM_PUBLIC_ROOT=public
```

This will store files directly in `public/storage/` instead of `storage/app/public/`.

---

## Image Upload Process

### Category Image Upload

1. **Upload Form:** Admin uploads image via category create/edit form
2. **Controller:** [`CategoryController.php`](app/Http/Controllers/Admin/CategoryController.php)
3. **Storage:** Image saved to `storage/app/public/categories/`
4. **Database:** Path saved to `categories.image_path` (e.g., `categories/filename.jpg`)
5. **Display:** Accessed via `asset('storage/' . $category->image_path)`

### Product Image Upload

1. **Upload Form:** Admin uploads images via product create/edit form
2. **Service:** [`ProductService.php`](app/Services/ProductService.php)
3. **Storage:** Images saved to `storage/app/public/products/`
4. **Database:** Each path saved to `product_images.image_path` (e.g., `products/filename.jpg`)
5. **Display:** Accessed via `asset('storage/' . $image->image_path)`
6. **Limit:** Maximum 5 images per product

---

## Checking Storage on Your Host

### Via File Explorer (Windows)
Navigate to:
```
C:\xampp\htdocs\giftsrilanka\storage\app\public\
```

You should see:
- `categories/` folder (contains category images)
- `products/` folder (contains product images)

### Via Command Line
```bash
# List category images
dir storage\app\public\categories

# List product images
dir storage\app\public\products
```

### Via PHP Artisan
```bash
# Check if storage link exists
php artisan storage:link
```

---

## Troubleshooting

### Images Not Displaying?

1. **Check symlink exists:**
   ```bash
   php artisan storage:link
   ```

2. **Check file permissions:**
   - `storage/` folder should be writable (755 or 775)
   - `public/storage/` should exist

3. **Check .env configuration:**
   ```env
   APP_URL=http://localhost/giftsrilanka
   FILESYSTEM_DISK=public
   ```

4. **For cPanel hosting:**
   ```env
   FILESYSTEM_PUBLIC_ROOT=public
   ```

### Finding Uploaded Images

**Database Query (Categories):**
```sql
SELECT id, name, image_path FROM categories WHERE image_path IS NOT NULL;
```

**Database Query (Products):**
```sql
SELECT pi.id, p.name, pi.image_path 
FROM product_images pi 
JOIN products p ON pi.product_id = p.id;
```

---

## Summary

| Type | Storage Path | Database Field | Public URL |
|------|-------------|----------------|------------|
| **Category Images** | `storage/app/public/categories/` | `categories.image_path` | `/storage/categories/filename.jpg` |
| **Product Images** | `storage/app/public/products/` | `product_images.image_path` | `/storage/products/filename.jpg` |

**Full Host Path Example:**
- Categories: `c:/xampp/htdocs/giftsrilanka/storage/app/public/categories/`
- Products: `c:/xampp/htdocs/giftsrilanka/storage/app/public/products/`

**Public Access:**
- Categories: `http://localhost/giftsrilanka/storage/categories/filename.jpg`
- Products: `http://localhost/giftsrilanka/storage/products/filename.jpg`
