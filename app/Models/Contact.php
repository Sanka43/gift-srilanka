<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'admin_reply',
        'replied_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isReplied(): bool
    {
        return $this->replied_at !== null;
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
