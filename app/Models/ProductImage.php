<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPublicUrlAttribute(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $this->image_path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Normalize legacy absolute URLs pointing to /storage or /public/storage
            // so they work on any local host/domain.
            $parsedPath = parse_url($path, PHP_URL_PATH);
            if (is_string($parsedPath)) {
                $normalized = ltrim(str_replace('\\', '/', $parsedPath), '/');
                if (Str::startsWith($normalized, 'public/storage/')) {
                    $normalized = substr($normalized, strlen('public/storage/'));
                    return url('/storage/' . ltrim($normalized, '/'));
                }
                if (Str::startsWith($normalized, 'storage/')) {
                    $normalized = substr($normalized, strlen('storage/'));
                    return url('/storage/' . ltrim($normalized, '/'));
                }
            }
            return $path;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/storage/')) {
            $path = substr($path, strlen('public/storage/'));
        } elseif (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        } elseif (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return url('/storage/' . ltrim($path, '/'));
    }
}
