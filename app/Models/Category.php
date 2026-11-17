<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'status', 'image_path'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function scopeMainCategories(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->orderBy('name');
    }

    public function hasSubcategories(): bool
    {
        return $this->children()->exists();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $this->image_path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Normalize legacy absolute URLs to current host.
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
