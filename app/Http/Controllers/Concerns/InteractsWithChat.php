<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

trait InteractsWithChat
{
    protected function chatChannelName(Conversation $conversation): string
    {
        return 'chat.conversation.' . $conversation->id;
    }

    protected function chatBroadcastEnabled(): bool
    {
        return in_array(config('broadcasting.default'), ['pusher', 'reverb'], true);
    }

    protected function serializeConversation(Conversation $conversation): array
    {
        $conversation->loadMissing([
            'customer.role',
            'latestMessage.sender.role',
        ]);

        return [
            'id' => $conversation->id,
            'customer' => $this->serializeUser($conversation->customer) ?? $this->serializeGuestConversationCustomer(),
            'latest_message' => $conversation->latestMessage
                ? $this->serializeMessage($conversation->latestMessage)
                : null,
            'updated_at' => optional($conversation->updated_at)->toIso8601String(),
        ];
    }

    protected function serializeMessages(Collection $messages): array
    {
        return $messages
            ->map(fn (Message $message) => $this->serializeMessage($message))
            ->values()
            ->all();
    }

    protected function serializeMessage(Message $message): array
    {
        $message->loadMissing(['sender.role']);

        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'body' => $message->body,
            'attachment' => $message->hasAttachment() ? [
                'name' => $message->attachment_name,
                // Signed relative URL: <img> GET often lacks session; signature authorizes without cookies.
                'url' => URL::signedRoute('chat.attachments.show', ['message' => $message->id], null, false),
                'mime_type' => $message->attachment_mime_type,
                'size' => $message->attachment_size,
                'is_image' => $message->attachmentIsImage(),
                'is_pdf' => $message->attachmentIsPdf(),
            ] : null,
            'read_at' => optional($message->read_at)->toIso8601String(),
            'created_at' => optional($message->created_at)->toIso8601String(),
            'sender' => $this->serializeUser($message->sender) ?? $this->serializeGuestMessageSender($message),
        ];
    }

    protected function serializeUser(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatarUrl(),
            'initials' => $user->initials(),
            'is_admin' => $user->isAdmin(),
        ];
    }

    protected function serializeGuestConversationCustomer(): array
    {
        return [
            'id' => null,
            'name' => 'Guest Customer',
            'email' => null,
            'avatar_url' => null,
            'initials' => 'GC',
            'is_admin' => false,
        ];
    }

    protected function serializeGuestMessageSender(Message $message): ?array
    {
        if (! $message->isGuestMessage()) {
            return null;
        }

        return [
            'id' => null,
            'name' => $message->guest_sender_name ?: 'Guest',
            'email' => null,
            'avatar_url' => null,
            'initials' => 'G',
            'is_admin' => false,
        ];
    }

    protected function storeChatAttachment(UploadedFile $file): array
    {
        $disk = 'local';
        $path = $file->store('chat-attachments', $disk);

        return [
            'attachment_disk' => $disk,
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_mime_type' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    protected function deleteChatAttachment(?Message $message): void
    {
        if (! $message?->hasAttachment()) {
            return;
        }

        Storage::disk($message->attachment_disk ?: 'local')->delete($message->attachment_path);
    }
}
