@php
    $guestChatEnabled = (bool) config('contact.chat_guest_enabled', false);
    $currentUserData = auth()->check() ? [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'avatar_url' => auth()->user()->avatarUrl(),
        'initials' => auth()->user()->initials(),
        'is_admin' => auth()->user()->isAdmin(),
    ] : null;
    $chatIconPath = public_path('images/chatbox-icon.webp');
    $chatIconUrl = asset('images/chatbox-icon.webp').(is_file($chatIconPath) ? '?v='.filemtime($chatIconPath) : '');
@endphp

<div
    x-data="customerChatWidget({
        authenticated: @js(auth()->check()),
        currentUser: @js($currentUserData),
        defaultSupportProfile: @js([
            'name' => 'Customer Support',
            'avatar_url' => null,
            'initials' => 'CS',
        ]),
        endpoints: {
            conversation: @js((auth()->check() || $guestChatEnabled) ? route('chat.conversation') : null),
            sendMessage: @js((auth()->check() || $guestChatEnabled) ? route('chat.messages.store') : null),
            adminSummary: @js(auth()->check() && auth()->user()->isAdmin() ? route('admin.chats.summary') : null),
            adminChatBase: @js(auth()->check() && auth()->user()->isAdmin() ? url('/admin/chats') : null),
        },
        guestChatEnabled: @js($guestChatEnabled),
        authLinks: {
            login: @js(route('login')),
            register: @js(route('register')),
        },
        welcomeNote: @js(config('contact.chat_welcome_note') ?: 'Thanks for reaching out! Our team will get back to you here as soon as we can.'),
        guestAutoNote: @js(config('contact.chat_guest_auto_note')),
    })"
    class="fixed {{ request()->routeIs('checkout.index') ? 'bottom-24 lg:bottom-6' : 'bottom-5 sm:bottom-6' }} right-4 z-[70] sm:right-6"
>
    <div x-show="open" x-cloak class="chat-panel mb-3 w-[calc(100vw-1.5rem)] max-w-[25rem] overflow-hidden">
        <div class="chat-panel-header border-b border-white/45 px-4 py-3.5">
            <div class="flex items-center justify-between gap-3" x-show="isAdminUser()">
                <button
                    type="button"
                    x-show="isAdminUser() && adminView === 'chat'"
                    x-cloak
                    @click="backToAdminList()"
                    class="chat-icon-btn flex h-9 w-9 items-center justify-center rounded-full text-stone-600 transition"
                    aria-label="Back to customer list"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div x-show="!(isAdminUser() && adminView === 'chat')" class="h-9 w-9"></div>

                <div class="min-w-0 flex-1">
                    <p
                        class="truncate text-center font-display text-base font-semibold text-stone-900"
                        x-text="isAdminUser() ? (adminView === 'chat' ? supportProfile.name : 'Chats') : 'Chat'"
                    ></p>
                    <p class="mt-0.5 text-center text-[11px] text-stone-400" x-text="isAdminUser() ? (adminView === 'chat' ? 'Reply to customer' : 'Customer inbox') : (authenticated ? '' : 'Customer support')"></p>
                </div>

                <button type="button" @click="closePanel()" class="chat-icon-btn flex h-9 w-9 items-center justify-center rounded-full text-stone-500 transition" aria-label="Close chat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex min-w-0 items-center justify-between gap-3" :class="isAdminUser() ? 'mt-3' : ''" x-show="!isAdminUser() || adminView === 'chat'">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <template x-if="supportProfile.avatar_url">
                        <img :src="supportProfile.avatar_url" alt="" class="h-11 w-11 rounded-full object-cover ring-2 ring-white shadow-sm">
                    </template>
                    <template x-if="!supportProfile.avatar_url">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-100 text-sm font-semibold text-primary-700" x-text="supportProfile.initials"></div>
                    </template>
                    <div class="min-w-0">
                        <p class="truncate font-display text-base font-semibold text-stone-900" x-text="supportProfile.name"></p>
                        <p class="text-xs text-stone-500" x-text="isAdminUser() ? 'View new, read, and unread customer messages.' : (canUseCustomerChat() ? 'Send us a message and we will reply here.' : 'Sign in or create an account to chat.')"></p>
                    </div>
                </div>
                <button type="button" @click="closePanel()" x-show="!isAdminUser()" class="chat-icon-btn flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-stone-500 transition" aria-label="Close chat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="chat-body bg-[linear-gradient(180deg,#f8fbff_0%,#f1f5ff_45%,#eef2ff_100%)] px-4 py-4">
            <template x-if="!authenticated && !guestChatEnabled">
                <div class="space-y-4">
                    <div class="rounded-[1.5rem] bg-white p-5 text-sm leading-relaxed text-stone-600 shadow-sm ring-1 ring-stone-100">
                        Sign in or create an account to chat with the admin team about orders, products, and delivery updates.
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a :href="authLinks.register" class="inline-flex items-center justify-center rounded-full bg-primary-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-600">
                            Register
                        </a>
                        <a :href="authLinks.login" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">
                            Sign in
                        </a>
                    </div>
                </div>
            </template>

            <template x-if="authenticated && isAdminUser()">
                <div class="space-y-4">
                    <template x-if="error">
                        <div class="rounded-2xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700" x-text="error"></div>
                    </template>

                    <template x-if="adminView === 'list'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-2 rounded-[1.35rem] bg-white p-1.5 shadow-sm ring-1 ring-stone-100">
                                <button
                                    type="button"
                                    @click="adminListFilter = 'all'"
                                    class="rounded-full px-4 py-2.5 text-sm font-semibold transition"
                                    :class="adminListFilter === 'all' ? 'bg-[linear-gradient(135deg,#6558f5_0%,#4f46e5_100%)] text-white shadow-sm' : 'text-stone-600 hover:bg-stone-50'"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="adminListFilter = 'unread'"
                                    class="rounded-full px-4 py-2.5 text-sm font-semibold transition"
                                    :class="adminListFilter === 'unread' ? 'bg-[linear-gradient(135deg,#6558f5_0%,#4f46e5_100%)] text-white shadow-sm' : 'text-stone-600 hover:bg-stone-50'"
                                >
                                    Unread
                                </button>
                            </div>

                            <div class="rounded-[1.75rem] bg-white p-2 shadow-sm ring-1 ring-stone-100">
                                <div class="flex items-center justify-between px-3 py-2">
                                    <p class="text-sm font-semibold text-stone-900">Customer List</p>
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600" x-text="filteredAdminConversations.length"></span>
                                </div>

                                <div class="max-h-72 space-y-1 overflow-y-auto px-1 pb-1">
                                    <template x-if="filteredAdminConversations.length === 0">
                                        <div class="px-4 py-8 text-center text-sm text-stone-500">
                                            <span x-text="adminListFilter === 'unread' ? 'No unread messages.' : 'No customer messages yet.'"></span>
                                        </div>
                                    </template>

                                    <template x-for="conversationItem in filteredAdminConversations" :key="conversationItem.id">
                                        <button
                                            type="button"
                                            @click="openAdminConversation(conversationItem.id)"
                                            class="flex w-full items-start gap-3 rounded-[1.35rem] px-3 py-3 text-left transition hover:bg-stone-50"
                                        >
                                            <template x-if="conversationItem.customer.avatar_url">
                                                <img :src="conversationItem.customer.avatar_url" alt="" class="h-12 w-12 rounded-full object-cover shadow-sm">
                                            </template>
                                            <template x-if="!conversationItem.customer.avatar_url">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 text-xs font-semibold text-primary-700" x-text="conversationItem.customer.initials"></div>
                                            </template>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <template x-if="conversationItem.unread_messages_count > 0">
                                                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_3px_rgba(16,185,129,0.14)]"></span>
                                                            </template>
                                                            <p class="truncate text-sm font-semibold text-stone-900" x-text="conversationItem.customer.name"></p>
                                                        </div>
                                                        <p class="truncate text-xs text-stone-500" x-text="conversationItem.customer.email"></p>
                                                    </div>
                                                    <template x-if="conversationItem.unread_messages_count > 0">
                                                        <span class="shrink-0 rounded-full bg-primary-50 px-2 py-1 text-[10px] font-bold text-primary-700" x-text="conversationItem.unread_messages_count"></span>
                                                    </template>
                                                </div>

                                                <p class="mt-1 truncate text-xs text-stone-600" x-text="conversationItem.latest_message ? messagePreview(conversationItem.latest_message) : 'No messages yet'"></p>
                                                <div class="mt-3 flex items-center justify-between gap-3">
                                                    <span class="text-[11px] text-stone-400" x-text="formatTimestamp(conversationItem.latest_message?.created_at || conversationItem.updated_at)"></span>
                                                    <svg class="h-4 w-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="adminView === 'chat'">
                        <div class="space-y-4">
                    <div
                                x-ref="adminMessagesContainer"
                        class="chat-messages-area flex max-h-[24rem] min-h-[18rem] flex-col gap-3 overflow-y-auto rounded-[1.75rem] px-3 py-4"
                            >
                                <template x-if="loading">
                                    <div class="flex h-full items-center justify-center text-sm text-stone-500">Loading conversation...</div>
                                </template>

                                <template x-if="!loading && messages.length === 0">
                                    <div class="flex h-full items-center justify-center px-6 text-center text-sm text-stone-500">
                                        No messages in this conversation yet.
                                    </div>
                                </template>

                                <template x-for="message in messages" :key="message.id">
                                    <div class="flex" :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'">
                                        <div class="max-w-[82%]">
                                            <div
                                                class="space-y-3 rounded-[1.35rem] px-4 py-3 text-sm leading-relaxed shadow-sm"
                                                :class="isOwnMessage(message) ? 'bg-[linear-gradient(135deg,#22c55e_0%,#16a34a_100%)] text-white rounded-br-sm' : 'bg-white text-stone-700 rounded-bl-sm ring-1 ring-stone-100'"
                                            >
                                                <template x-if="message.body">
                                                    <p class="whitespace-pre-line break-words" x-text="message.body"></p>
                                                </template>
                                                <template x-if="message.attachment">
                                                    <div class="space-y-2">
                                                        <template x-if="attachmentShowsImagePreview(message)">
                                                            <a :href="message.attachment.url" target="_blank" rel="noopener" class="block overflow-hidden rounded-2xl bg-white/10 ring-1 ring-black/5">
                                                                <img :src="message.attachment.url" :alt="message.attachment.name" class="max-h-56 w-full object-cover">
                                                            </a>
                                                        </template>
                                                        <a :href="message.attachment.url" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl px-3 py-2" :class="isOwnMessage(message) ? 'bg-white/15 text-white' : 'bg-stone-50 text-stone-700 ring-1 ring-stone-200'">
                                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl" :class="isOwnMessage(message) ? 'bg-white/20' : 'bg-primary-50 text-primary-700'">
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

                            <form @submit.prevent="sendAdminReply()" class="space-y-3">
                                <label for="admin-inline-chat-message" class="sr-only">Reply to customer</label>
                                <input
                                    x-ref="adminAttachmentInput"
                                    type="file"
                                    class="hidden"
                                    accept="image/*,application/pdf"
                                    @change="handleAttachmentSelection($event)"
                                >
                                <template x-if="attachmentName">
                                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3 text-sm text-stone-600 shadow-sm ring-1 ring-stone-100">
                                        <span class="truncate" x-text="attachmentName"></span>
                                        <button type="button" @click="clearAttachment('adminAttachmentInput')" class="rounded-full px-2 py-1 text-xs font-semibold text-stone-500 transition hover:bg-stone-100 hover:text-stone-700">
                                            Remove
                                        </button>
                                    </div>
                                </template>
                                <div class="chat-composer flex items-end gap-2 rounded-full p-2">
                                    <button type="button" @click="openAttachmentPicker('adminAttachmentInput')" class="chat-composer-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-stone-500 transition" aria-label="Attach image or PDF">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                    <textarea
                                        id="admin-inline-chat-message"
                                        x-model="draft"
                                        rows="1"
                                        maxlength="2000"
                                        placeholder="Type your message"
                                        class="min-h-10 flex-1 resize-none border-0 bg-transparent px-1 py-2 text-sm text-stone-700 outline-none placeholder:text-stone-400 focus:ring-0"
                                    ></textarea>
                                    <button
                                        type="submit"
                                        class="chat-send-btn inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white transition disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="sending || !canSendMessage() || !conversation"
                                        aria-label="Send reply"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m0 0l-5-5m5 5l-5 5" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="canUseCustomerChat()">
                <div class="space-y-4">
                    <div
                        x-ref="messagesContainer"
                        class="chat-messages-area flex max-h-[24rem] min-h-[18rem] flex-col gap-3 overflow-y-auto rounded-[1.75rem] px-3 py-4"
                    >
                        <template x-if="loading">
                            <div class="flex h-full items-center justify-center text-sm text-stone-500">Loading conversation...</div>
                        </template>

                        <template x-if="!loading && messages.length === 0">
                            <div class="flex h-full items-center justify-center px-6 text-center text-sm text-stone-500">
                                Start the conversation and an admin will reply here.
                            </div>
                        </template>

                        <template x-if="!loading && showWelcomeNote && welcomeNote">
                            <div class="flex justify-start">
                                <div class="max-w-[92%] rounded-[1.35rem] border border-primary-100/80 bg-white px-4 py-3 text-sm leading-relaxed text-stone-700 shadow-sm ring-1 ring-stone-100">
                                    <p class="font-semibold text-primary-800">Welcome</p>
                                    <p class="mt-1.5 text-stone-600" x-text="welcomeNote"></p>
                                </div>
                            </div>
                        </template>

                        <template x-for="message in messages" :key="message.id">
                            <div class="flex" :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[82%]">
                                    <div
                                        class="space-y-3 rounded-[1.35rem] px-4 py-3 text-sm leading-relaxed shadow-sm"
                                        :class="isOwnMessage(message) ? 'bg-[linear-gradient(135deg,#22c55e_0%,#16a34a_100%)] text-white rounded-br-sm' : 'bg-white text-stone-700 rounded-bl-sm ring-1 ring-stone-100'"
                                    >
                                        <template x-if="message.body">
                                            <p class="whitespace-pre-line break-words" x-text="message.body"></p>
                                        </template>
                                        <template x-if="message.attachment">
                                            <div class="space-y-2">
                                                <template x-if="attachmentShowsImagePreview(message)">
                                                    <a :href="message.attachment.url" target="_blank" rel="noopener" class="block overflow-hidden rounded-2xl bg-white/10 ring-1 ring-black/5">
                                                        <img :src="message.attachment.url" :alt="message.attachment.name" class="max-h-56 w-full object-cover">
                                                    </a>
                                                </template>
                                                <a :href="message.attachment.url" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl px-3 py-2" :class="isOwnMessage(message) ? 'bg-white/15 text-white' : 'bg-stone-50 text-stone-700 ring-1 ring-stone-200'">
                                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl" :class="isOwnMessage(message) ? 'bg-white/20' : 'bg-primary-50 text-primary-700'">
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

                    <template x-if="error">
                        <div class="rounded-2xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700" x-text="error"></div>
                    </template>

                    <form @submit.prevent="sendMessage()" class="space-y-3">
                        <label for="customer-chat-message" class="sr-only">Type your message</label>
                        <input
                            x-ref="customerAttachmentInput"
                            type="file"
                            class="hidden"
                            accept="image/*,application/pdf"
                            @change="handleAttachmentSelection($event)"
                        >
                        <template x-if="attachmentName">
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3 text-sm text-stone-600 shadow-sm ring-1 ring-stone-100">
                                <span class="truncate" x-text="attachmentName"></span>
                                <button type="button" @click="clearAttachment('customerAttachmentInput')" class="rounded-full px-2 py-1 text-xs font-semibold text-stone-500 transition hover:bg-stone-100 hover:text-stone-700">
                                    Remove
                                </button>
                            </div>
                        </template>
                        <div class="chat-composer flex items-end gap-2 rounded-full p-2">
                            <button type="button" @click="openAttachmentPicker('customerAttachmentInput')" class="chat-composer-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-stone-500 transition" aria-label="Attach image or PDF">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                            <textarea
                                id="customer-chat-message"
                                x-model="draft"
                                rows="1"
                                maxlength="2000"
                                placeholder="Type your message"
                                class="min-h-10 flex-1 resize-none border-0 bg-transparent px-1 py-2 text-sm text-stone-700 outline-none placeholder:text-stone-400 focus:ring-0"
                            ></textarea>
                            <button
                                type="submit"
                                class="chat-send-btn inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white transition disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="sending || !canSendMessage()"
                                aria-label="Send message"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m0 0l-5-5m5 5l-5 5" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <button
        type="button"
        @click="togglePanel()"
        x-show="!open"
        x-cloak
        class="relative inline-flex origin-center cursor-pointer appearance-none border-0 !bg-transparent p-0 shadow-none outline-none transition duration-300 animate-chat-launcher hover:animate-none hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/55 focus-visible:ring-offset-0 motion-reduce:animate-none"
        aria-label="Open customer chat"
    >
        <img
            src="{{ $chatIconUrl }}"
            alt=""
            role="presentation"
            class="pointer-events-none block h-36 w-36 max-w-none select-none object-contain object-center md:h-52 md:w-52"
            width="208"
            height="208"
            decoding="async"
        />
        <template x-if="isAdminUser() && adminSummary.unread_messages > 0">
            <span class="absolute -right-1.5 -top-1.5 flex min-h-6 min-w-6 items-center justify-center rounded-full bg-emerald-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-white shadow-lg" x-text="adminSummary.unread_messages > 99 ? '99+' : adminSummary.unread_messages"></span>
        </template>
        <template x-if="!isAdminUser() && authenticated && customerUnreadCount > 0">
            <span class="absolute -right-1.5 -top-1.5 flex min-h-6 min-w-6 items-center justify-center rounded-full bg-emerald-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-white shadow-lg" x-text="customerUnreadCount > 99 ? '99+' : customerUnreadCount"></span>
        </template>
    </button>
    <style>
        .chat-panel {
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.62);
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.96) 0%, rgba(244, 247, 255, 0.95) 100%);
            box-shadow: 0 25px 65px -24px rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(14px);
        }

        .chat-panel-header {
            background: linear-gradient(120deg, rgba(236, 244, 255, 0.95) 0%, rgba(247, 242, 255, 0.92) 100%);
        }

        .chat-body {
            position: relative;
        }

        .chat-body::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 88% 8%, rgba(99, 102, 241, 0.08), transparent 42%);
        }

        .chat-messages-area {
            background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .chat-icon-btn:hover {
            background: rgba(148, 163, 184, 0.14);
            color: rgb(28, 25, 23);
        }

        .chat-composer {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 250, 252, 0.98) 100%);
            border: 1px solid rgba(203, 213, 225, 0.9);
            box-shadow: 0 14px 35px -24px rgba(51, 65, 85, 0.75);
        }

        .chat-composer-icon:hover {
            background: rgba(99, 102, 241, 0.1);
            color: rgb(79, 70, 229);
        }

        .chat-send-btn {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 10px 22px -12px rgba(79, 70, 229, 0.9);
        }

        .chat-send-btn:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
        }

        /* Mobile: reduce launcher icon size only (desktop remains unchanged). */
        @media (max-width: 767px) {
            button[aria-label="Open customer chat"] img {
                width: 9rem !important;  
                height: 9rem !important; 
            }
        }
    </style>
</div>
