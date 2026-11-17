<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

$authorizeConversationChannel = function (User $user, int $conversationId) {
    if ($user->isAdmin()) {
        return true;
    }

    return Conversation::query()
        ->whereKey($conversationId)
        ->where('customer_id', $user->id)
        ->exists();
};

Broadcast::channel('chat.conversation.{conversationId}', $authorizeConversationChannel);
Broadcast::channel('private-chat.conversation.{conversationId}', $authorizeConversationChannel);
