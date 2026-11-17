<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'discount_price', 'stock', 'status', 'featured',
        'occasion', 'recipient', 'delivery_type', 'rating', 'in_stock', 'is_on_sale',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'status' => 'boolean',
            'featured' => 'boolean',
            'rating' => 'decimal:1',
            'in_stock' => 'boolean',
            'is_on_sale' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function stockNotifications(): HasMany
    {
        return $this->hasMany(StockNotification::class);
    }

    /**
     * Prevent stock from going negative (application-level safeguard).
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (isset($product->stock) && $product->stock < 0) {
                $product->stock = 0;
            }
        });
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    /**
     * Recalculate and persist rating from reviews. Call after review create/update/delete.
     */
    public function updateRatingFromReviews(): void
    {
        $avg = (float) $this->reviews()->avg('rating');
        $this->updateQuietly(['rating' => round($avg, 1)]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
