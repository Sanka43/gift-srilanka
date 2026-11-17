@extends('layouts.admin')

@section('title', 'Chats')

@section('content')
@php
    $currentAdmin = [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'avatar_url' => auth()->user()->avatarUrl(),
        'initials' => auth()->user()->initials(),
        'is_admin' => true,
    ];
@endphp

<div
    x-data="adminChatPanel({
        chatIndexUrl: @js(route('admin.chats.index')),
        chatBaseUrl: @js(url('/admin/chats')),
        currentUser: @js($currentAdmin),
        initialConversations: @js($initialConversations),
        initialConversation: @js($initialSelectedConversation),
        initialMessages: @js($initialSelectedMessages),
        pollingIntervalMs: @js($chatPollingIntervalMs),
        broadcastingEnabled: @js($chatBroadcastEnabled),
    })"
    class="grid gap-6 xl:grid-cols-[22rem,minmax(0,1fr)]"
>
    <section class="admin-card overflow-hidden border border-stone-200 bg-white dark:bg-stone-800 dark:border-stone-700">
        <div class="border-b border-stone-100 px-5 py-4 dark:border-stone-700">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-display text-lg font-semibold text-stone-900 dark:text-stone-100">Inbox</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">Customer conversations</p>
                </div>
                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300" x-text="filteredConversations.length"></span>
            </div>
            <label for="admin-chat-search" class="sr-only">Search chats</label>
            <input
                id="admin-chat-search"
                x-model.debounce.250ms="search"
                type="search"
                placeholder="Search customer or message..."
                class="admin-input mt-4 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-stone-600 dark:bg-stone-900/40 dark:text-stone-200"
            >
        </div>

        <div class="max-h-[70vh] overflow-y-auto">
            <template x-if="filteredConversations.length === 0">
                <div class="px-5 py-10 text-center text-sm text-stone-500 dark:text-stone-400">
                    No conversations yet.
                </div>
            </template>

            <template x-for="conversation in filteredConversations" :key="conversation.id">
                <button
                    type="button"
                    @click="selectConversation(conversation.id)"
                    class="flex w-full items-start gap-3 border-b border-stone-100 px-5 py-4 text-left transition hover:bg-stone-50 dark:border-stone-700 dark:hover:bg-stone-700/40"
                    :class="selectedConversation && selectedConversation.id === conversation.id ? 'bg-primary-50 dark:bg-primary-900/20' : ''"
                >
                    <template x-if="conversation.customer.avatar_url">
                        <img :src="conversation.customer.avatar_url" alt="" class="h-11 w-11 rounded-2xl object-cover">
                    </template>
                    <template x-if="!conversation.customer.avatar_url">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-100 text-sm font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300" x-text="conversation.customer.initials"></div>
                    </template>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate text-sm font-semibold text-stone-900 dark:text-stone-100" x-text="conversation.customer.name"></p>
                            <span class="shrink-0 text-[11px] text-stone-400" x-text="formatTimestamp(conversation.latest_message?.created_at || conversation.updated_at)"></span>
                        </div>
                        <p class="truncate text-xs text-stone-500 dark:text-stone-400" x-text="conversation.customer.email"></p>
                        <p class="mt-2 truncate text-sm text-stone-600 dark:text-stone-300" x-text="conversation.latest_message ? messagePreview(conversation.latest_message) : 'No messages yet'"></p>
                    </div>
                </button>
            </template>
        </div>
    </section>

    <section class="admin-card flex min-h-[70vh] flex-col overflow-hidden border border-stone-200 bg-white dark:bg-stone-800 dark:border-stone-700">
        <template x-if="!selectedConversation">
            <div class="flex h-full min-h-[24rem] items-center justify-center px-6 text-center text-sm text-stone-500 dark:text-stone-400">
                Select a conversation to read and reply.
            </div>
        </template>

        <template x-if="selectedConversation">
            <div class="flex h-full flex-col">
                <div class="flex items-center gap-4 border-b border-stone-100 px-6 py-4 dark:border-stone-700">
                    <template x-if="selectedConversation.customer.avatar_url">
                        <img :src="selectedConversation.customer.avatar_url" alt="" class="h-12 w-12 rounded-2xl object-cover">
                    </template>
                    <template x-if="!selectedConversation.customer.avatar_url">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-sm font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300" x-text="selectedConversation.customer.initials"></div>
                    </template>
                    <div class="min-w-0">
                        <h2 class="truncate font-display text-xl font-semibold text-stone-900 dark:text-stone-100" x-text="selectedConversation.customer.name"></h2>
                        <p class="truncate text-sm text-stone-500 dark:text-stone-400" x-text="selectedConversation.customer.email"></p>
                    </div>
                </div>

                <div x-ref="adminMessagesContainer" class="flex-1 space-y-4 overflow-y-auto bg-stone-50 px-6 py-5 dark:bg-stone-900/30">
                    <template x-if="conversationLoading">
                        <div class="flex h-full min-h-[18rem] items-center justify-center text-sm text-stone-500 dark:text-stone-400">Loading messages...</div>
                    </template>

                    <template x-if="!conversationLoading && messages.length === 0">
                        <div class="flex h-full min-h-[18rem] items-center justify-center text-sm text-stone-500 dark:text-stone-400">No messages in this conversation yet.</div>
                    </template>

                    <template x-for="message in messages" :key="message.id">
                        <div class="flex" :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[80%]">
                                <div class="mb-1 flex items-center gap-2 px-1" :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'">
                                    <template x-if="!isOwnMessage(message)">
                                        <template x-if="message.sender.avatar_url">
                                            <img :src="message.sender.avatar_url" alt="" class="h-7 w-7 rounded-full object-cover">
                                        </template>
                                    </template>
                                    <template x-if="!isOwnMessage(message) && !message.sender.avatar_url">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-stone-200 text-xs font-semibold text-stone-700 dark:bg-stone-700 dark:text-stone-200" x-text="message.sender.initials"></div>
                                    </template>
                                    <span class="text-xs font-semibold text-stone-500 dark:text-stone-400" x-text="message.sender.name"></span>
                                    <template x-if="isOwnMessage(message) && currentUser.avatar_url">
                                        <img :src="currentUser.avatar_url" alt="" class="h-7 w-7 rounded-full object-cover">
                                    </template>
                                </div>
                                <div
                                    class="space-y-3 rounded-2xl px-4 py-3 text-sm leading-relaxed"
                                    :class="isOwnMessage(message) ? 'rounded-br-md bg-emerald-500 text-white' : 'rounded-bl-md border border-stone-200 bg-white text-stone-700 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100'"
                                >
                                    <template x-if="message.body">
                                        <p class="whitespace-pre-line break-words" x-text="message.body"></p>
                                    </template>
                                    <template x-if="message.attachment">
                                        <div class="space-y-2">
                                            <template x-if="message.attachment.is_image">
                                                <a :href="message.attachment.url" target="_blank" rel="noopener" class="block overflow-hidden rounded-2xl bg-white/10 ring-1 ring-black/5">
                                                    <img :src="message.attachment.url" :alt="message.attachment.name" class="max-h-72 w-full object-cover">
                                                </a>
                                            </template>
                                            <a :href="message.attachment.url" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl px-3 py-2" :class="isOwnMessage(message) ? 'bg-white/15 text-white' : 'bg-stone-50 text-stone-700 ring-1 ring-stone-200 dark:bg-stone-900/50 dark:text-stone-100 dark:ring-stone-700'">
                                                <span class="flex h-10 w-10 items-center justify-center rounded-xl" :class="isOwnMessage(message) ? 'bg-white/20' : 'bg-primary-50 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300'">
                                                    <template x-if="message.attachment.is_pdf">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h6L18 8.25v10.5A1.5 1.5 0 0116.5 20.25h-9A1.5 1.5 0 016 18.75v-13.5A1.5 1.5 0 017.5 3.75z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75v4.5H18" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="!message.attachment.is_pdf">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 15.75l4.94-4.94a1.5 1.5 0 012.12 0l2.38 2.38 4.69-4.69a1.5 1.5 0 012.12 0l.25.25v7.5A1.5 1.5 0 0118.75 18h-13.5a1.5 1.5 0 01-1.5-1.5v-.75z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75h.008v.008h-.008V6.75z" />
                                                        </svg>
                                                    </template>
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block truncate text-sm font-medium" x-text="message.attachment.name"></span>
                                                    <span class="block text-[11px] opacity-80" x-text="formatAttachmentSize(message.attachment.size)"></span>
                                                </span>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                                <p class="mt-1 px-1 text-[11px] text-stone-400" :class="isOwnMessage(message) ? 'text-right' : 'text-left'" x-text="formatTimestamp(message.created_at)"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-stone-100 px-6 py-5 dark:border-stone-700">
                    <template x-if="error">
                        <div class="mb-3 rounded-2xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700" x-text="error"></div>
                    </template>

                    <form @submit.prevent="sendMessage()" class="space-y-3">
                        <label for="admin-chat-message" class="sr-only">Reply</label>
                        <input
                            x-ref="adminAttachmentInput"
                            type="file"
                            class="hidden"
                            accept="image/*,application/pdf"
                            @change="handleAttachmentSelection($event)"
                        >
                        <template x-if="attachmentName">
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600 dark:border-stone-600 dark:bg-stone-900/40 dark:text-stone-300">
                                <span class="truncate" x-text="attachmentName"></span>
                                <button type="button" @click="clearAttachment()" class="rounded-full px-2 py-1 text-xs font-semibold text-stone-500 transition hover:bg-stone-200/70 hover:text-stone-700 dark:hover:bg-stone-700 dark:hover:text-stone-100">
                                    Remove
                                </button>
                            </div>
                        </template>
                        <textarea
                            id="admin-chat-message"
                            x-model="draft"
                            rows="3"
                            maxlength="2000"
                            placeholder="Reply to the customer..."
                            class="admin-input w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-700 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-stone-600 dark:bg-stone-900/40 dark:text-stone-200"
                        ></textarea>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <button type="button" @click="openAttachmentPicker()" class="inline-flex items-center justify-center rounded-2xl border border-stone-200 px-3 py-2 text-sm font-semibold text-stone-600 transition hover:bg-stone-50 dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700/40">
                                    Attach image/PDF
                                </button>
                                <p class="text-[11px] text-stone-400">Replies appear instantly when broadcasting is configured.</p>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="sending || !canSendMessage() || !selectedConversation"
                            >
                                <span x-text="sending ? 'Sending...' : 'Send reply'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </section>
</div>
@endsection
