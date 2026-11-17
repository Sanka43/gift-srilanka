<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'guest_sender_name',
        'body',
        'attachment_disk',
        'attachment_path',
        'attachment_name',
        'attachment_mime_type',
        'attachment_size',
        'read_at',
    ];

    protected $touches = ['conversation'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'attachment_size' => 'integer',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    public function attachmentIsImage(): bool
    {
        return str_starts_with((string) $this->attachment_mime_type, 'image/');
    }

    public function attachmentIsPdf(): bool
    {
        return $this->attachment_mime_type === 'application/pdf';
    }

    public function isGuestMessage(): bool
    {
        return $this->sender_id === null;
    }
}
