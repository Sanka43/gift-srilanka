<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Concerns\InteractsWithChat;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Conversation;
use App\Models\User;
use App\Support\ChatGuestIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerChatController extends Controller
{
    use InteractsWithChat;

    private const POLLING_INTERVAL_MS = 5000;

    public function __construct(private readonly ChatGuestIdentity $chatGuestIdentity)
    {
    }

    public function conversation(Request $request): JsonResponse
    {
        $this->ensureCustomerUser($request);

        $conversation = $this->conversationForRequest($request, false);

        if (! $conversation) {
            return response()->json([
                'conversation' => null,
                'messages' => [],
                'unread_messages_count' => 0,
                'channel' => null,
                'broadcasting' => [
                    'enabled' => false,
                ],
                'polling_interval_ms' => (int) config('contact.chat_polling_interval_ms', self::POLLING_INTERVAL_MS),
            ]);
        }

        return $this->conversationResponse($conversation, $request->boolean('mark_read', true));
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureCustomerUser($request);
        abort_unless($conversation->customer_id === $request->user()->id, 403);

        return $this->conversationResponse($conversation, true);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureCustomerUser($request);
        abort_unless($conversation->customer_id === $request->user()->id, 403);

        $this->markAdminMessagesAsRead($conversation);
        $conversation->load(['messages.sender.role']);

        return response()->json([
            'messages' => $this->serializeMessages($conversation->messages),
        ]);
    }

    public function store(StoreChatMessageRequest $request): JsonResponse
    {
        $this->ensureCustomerUser($request);

        $conversation = $this->conversationForRequest($request, true);
        $attachment = $request->file('attachment');

        $messageAttributes = [
            'body' => $request->validated('body') ?? '',
        ];

        if ($request->user()) {
            $messageAttributes['sender_id'] = $request->user()->id;
        } else {
            $messageAttributes['sender_id'] = null;
            $messageAttributes['guest_sender_name'] = $request->validated('guest_name') ?: 'Guest';
        }

        if ($attachment) {
            $messageAttributes = [
                ...$messageAttributes,
                ...$this->storeChatAttachment($attachment),
            ];
        }

        $message = $conversation->messages()->create($messageAttributes);

        $message->load(['sender.role', 'conversation.customer.role']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => $this->serializeMessage($message),
            'conversation' => $this->serializeConversation($conversation->fresh()),
            'channel' => $this->chatChannelName($conversation),
        ], 201);
    }

    private function conversationForRequest(Request $request, bool $createIfMissing = true): ?Conversation
    {
        if ($request->user()) {
            return $this->conversationForUser($request->user(), $createIfMissing);
        }

        $guestToken = $this->chatGuestIdentity->resolveToken($request);

        abort_unless($guestToken, 403, 'Guest chat is currently unavailable.');

        if (! $createIfMissing) {
            return Conversation::query()->where('guest_token', $guestToken)->first();
        }

        return Conversation::firstOrCreate(['guest_token' => $guestToken]);
    }

    private function conversationForUser(User $user, bool $createIfMissing = true): ?Conversation
    {
        if (! $createIfMissing) {
            return Conversation::query()->where('customer_id', $user->id)->first();
        }

        return Conversation::firstOrCreate(['customer_id' => $user->id]);
    }

    private function conversationResponse(Conversation $conversation, bool $markRead): JsonResponse
    {
        if ($markRead) {
            $this->markAdminMessagesAsRead($conversation);
        }

        $conversation->load([
            'customer.role',
            'messages.sender.role',
            'latestMessage.sender.role',
        ]);

        return response()->json([
            'conversation' => $this->serializeConversation($conversation),
            'messages' => $this->serializeMessages($conversation->messages),
            'unread_messages_count' => $this->countUnreadAdminMessages($conversation),
            'channel' => $this->chatChannelName($conversation),
            'broadcasting' => [
                'enabled' => (bool) $conversation->customer_id && $this->chatBroadcastEnabled(),
            ],
            'polling_interval_ms' => (int) config('contact.chat_polling_interval_ms', self::POLLING_INTERVAL_MS),
        ]);
    }

    private function ensureCustomerUser(Request $request): void
    {
        if ($request->user()?->isAdmin()) {
            abort(403, 'Admins can only reply from the admin chat panel.');
        }

        if (! $request->user() && ! $this->chatGuestIdentity->isEnabled()) {
            abort(401, 'Please sign in to chat with support.');
        }
    }

    private function markAdminMessagesAsRead(Conversation $conversation): void
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->where(function ($query) use ($conversation) {
                if ($conversation->customer_id) {
                    $query->where('sender_id', '!=', $conversation->customer_id);
                } else {
                    $query->whereNotNull('sender_id');
                }
            })
            ->update(['read_at' => now()]);
    }

    private function countUnreadAdminMessages(Conversation $conversation): int
    {
        return $conversation->messages()
            ->whereNull('read_at')
            ->where(function ($query) use ($conversation) {
                if ($conversation->customer_id) {
                    $query->where('sender_id', '!=', $conversation->customer_id);
                } else {
                    $query->whereNotNull('sender_id');
                }
            })
            ->count();
    }
}
