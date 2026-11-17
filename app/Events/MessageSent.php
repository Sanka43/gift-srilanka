<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Message $message
    ) {
        $this->message->loadMissing(['sender.role', 'conversation.customer.role']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'body' => $this->message->body,
                'attachment' => $this->message->hasAttachment() ? [
                    'name' => $this->message->attachment_name,
                    'url' => URL::signedRoute('chat.attachments.show', ['message' => $this->message->id], null, false),
                    'mime_type' => $this->message->attachment_mime_type,
                    'size' => $this->message->attachment_size,
                    'is_image' => $this->message->attachmentIsImage(),
                    'is_pdf' => $this->message->attachmentIsPdf(),
                ] : null,
                'read_at' => optional($this->message->read_at)->toIso8601String(),
                'created_at' => optional($this->message->created_at)->toIso8601String(),
                'sender' => [
                    'id' => $sender?->id,
                    'name' => $sender?->name,
                    'email' => $sender?->email,
                    'avatar_url' => $sender?->avatarUrl(),
                    'initials' => $sender?->initials(),
                    'is_admin' => $sender?->isAdmin() ?? false,
                ],
            ],
        ];
    }
}
