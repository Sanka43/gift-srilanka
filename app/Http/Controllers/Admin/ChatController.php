<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    use InteractsWithChat;

    private const POLLING_INTERVAL_MS = 5000;

    public function index(Request $request): View|JsonResponse
    {
        $conversationQuery = Conversation::query()
            ->with(['customer.role', 'latestMessage.sender.role'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->string('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })->orWhereHas('messages', function ($messageQuery) use ($search) {
                        $messageQuery->where('body', 'like', '%' . $search . '%');
                    });
                });
            })
            ->latest('updated_at');

        $conversations = $conversationQuery
            ->paginate(20)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'conversations' => $conversations
                    ->getCollection()
                    ->map(fn (Conversation $conversation) => $this->serializeConversation($conversation))
                    ->values(),
            ]);
        }

        $currentConversations = $conversations->getCollection();
        $selectedConversationId = $request->filled('conversation')
            ? (int) $request->integer('conversation')
            : null;

        $selectedConversation = $request->filled('conversation')
            ? $currentConversations->firstWhere('id', $selectedConversationId)
            : $currentConversations->first();

        if (! $selectedConversation && $selectedConversationId) {
            $selectedConversation = Conversation::query()
                ->with(['customer.role', 'latestMessage.sender.role'])
                ->find($selectedConversationId);
        }

        if ($selectedConversation) {
            $this->markCustomerMessagesAsRead($selectedConversation);
            $selectedConversation->load(['messages.sender.role']);
        }

        return view('admin.chats.index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'selectedMessages' => $selectedConversation?->messages ?? collect(),
            'initialConversations' => $currentConversations
                ->map(fn (Conversation $conversation) => $this->serializeConversation($conversation))
                ->values()
                ->all(),
            'initialSelectedConversation' => $selectedConversation
                ? $this->serializeConversation($selectedConversation)
                : null,
            'initialSelectedMessages' => $selectedConversation
                ? $this->serializeMessages($selectedConversation->messages)
                : [],
            'chatPollingIntervalMs' => self::POLLING_INTERVAL_MS,
            'chatBroadcastEnabled' => $this->chatBroadcastEnabled(),
        ]);
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->markCustomerMessagesAsRead($conversation);
        $conversation->load([
            'customer.role',
            'messages.sender.role',
            'latestMessage.sender.role',
        ]);

        return response()->json([
            'conversation' => $this->serializeConversation($conversation),
            'messages' => $this->serializeMessages($conversation->messages),
            'channel' => $this->chatChannelName($conversation),
            'broadcasting' => [
                'enabled' => $this->chatBroadcastEnabled(),
            ],
            'polling_interval_ms' => self::POLLING_INTERVAL_MS,
        ]);
    }

    public function summary(): JsonResponse
    {
        $customerMessageQuery = Message::query()
            ->whereHas('conversation', function ($query) {
                $query->where(function ($conversationQuery) {
                    $conversationQuery
                        ->whereColumn('customer_id', 'messages.sender_id')
                        ->orWhere(function ($guestConversationQuery) {
                            $guestConversationQuery
                                ->whereNull('customer_id')
                                ->whereNotNull('guest_token')
                                ->whereNull('messages.sender_id');
                        });
                });
            });

        $conversations = Conversation::query()
            ->with(['customer.role', 'latestMessage.sender.role'])
            ->withCount([
                'messages as unread_messages_count' => function ($query) {
                    $query
                        ->whereNull('read_at')
                        ->where(function ($messageQuery) {
                            $messageQuery
                                ->whereColumn('sender_id', 'conversations.customer_id')
                                ->orWhere(function ($guestMessageQuery) {
                                    $guestMessageQuery
                                        ->whereNull('sender_id')
                                        ->whereNull('conversations.customer_id')
                                        ->whereNotNull('conversations.guest_token');
                                });
                        });
                },
            ])
            ->latest('updated_at')
            ->take(8)
            ->get();

        return response()->json([
            'stats' => [
                'new_messages' => (clone $customerMessageQuery)
                    ->whereDate('created_at', today())
                    ->count(),
                'unread_messages' => (clone $customerMessageQuery)
                    ->whereNull('read_at')
                    ->count(),
                'read_messages' => (clone $customerMessageQuery)
                    ->whereNotNull('read_at')
                    ->count(),
            ],
            'conversations' => $conversations->map(function (Conversation $conversation) {
                return [
                    ...$this->serializeConversation($conversation),
                    'unread_messages_count' => (int) ($conversation->unread_messages_count ?? 0),
                    'admin_chat_url' => route('admin.chats.index', ['conversation' => $conversation->id]),
                ];
            })->values(),
            'admin_chats_url' => route('admin.chats.index'),
        ]);
    }

    public function store(StoreChatMessageRequest $request, Conversation $conversation): JsonResponse
    {
        if (! $conversation->messages()
            ->where(function ($query) use ($conversation) {
                $query->where('sender_id', $conversation->customer_id);

                if ($conversation->isGuestConversation()) {
                    $query->orWhereNull('sender_id');
                }
            })
            ->exists()) {
            return response()->json([
                'message' => 'Admins can only reply after a customer sends a message.',
            ], 422);
        }

        $attachment = $request->file('attachment');

        $messageAttributes = [
            'sender_id' => $request->user()->id,
            'body' => $request->validated('body') ?? '',
        ];

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

    private function markCustomerMessagesAsRead(Conversation $conversation): void
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->where(function ($query) use ($conversation) {
                $query->where('sender_id', $conversation->customer_id);

                if ($conversation->isGuestConversation()) {
                    $query->orWhereNull('sender_id');
                }
            })
            ->update(['read_at' => now()]);
    }
}
