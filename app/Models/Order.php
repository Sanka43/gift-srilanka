<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'full_name',
        'phone',
        'address',
        'district',
        'delivery_date',
        'delivery_time',
        'special_note',
        'total_amount',
        'delivery_charge',
        'status',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_gateway',
        'payment_meta',
        'estimated_delivery_date',
        'cancel_requested',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'delivery_date' => 'date',
            'delivery_time' => 'string',
            'estimated_delivery_date' => 'date',
            'cancel_requested' => 'boolean',
            'payment_meta' => 'array',
        ];
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user_id ? $this->user?->email : $this->guest_email;
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user_id ? ($this->user?->name ?? $this->full_name) : ($this->guest_name ?? $this->full_name);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }
}
