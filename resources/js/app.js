import './bootstrap';
import { initSnowfall } from './snow';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

window.Alpine = Alpine;
Alpine.plugin(intersect);

Alpine.data('snowThemeController', () => ({
    enabled: true,
    init() {
        this.updateState();
        window.addEventListener('snow:changed', (e) => {
            this.enabled = e.detail.enabled;
        });
    },
    updateState() {
        if (window.GiftSriLankaSnow) {
            this.enabled = window.GiftSriLankaSnow.enabled;
        } else {
            const stored = localStorage.getItem('giftsrilanka_snow_enabled');
            this.enabled = stored !== null ? stored === 'true' : true;
        }
    },
    toggle() {
        if (window.GiftSriLankaSnow) {
            window.GiftSriLankaSnow.toggle();
        } else {
            window.dispatchEvent(new CustomEvent('snow:toggle'));
        }
        this.updateState();
    }
}));

// Initialize Snow Theme
initSnowfall();

Alpine.data('heroSection', () => ({}));

Alpine.data('scrollParallax', () => {
    let scrollY = 0;
    let raf = null;
    const factor = 0.12;
    const maxMove = 28;
    return {
        scrollY: 0,
        leftX: 0,
        rightX: 0,
        init() {
            const update = () => {
                const y = window.scrollY || window.pageYOffset;
                this.scrollY = y;
                const move = Math.min(y * factor, maxMove);
                this.leftX = -move;
                this.rightX = move;
            };
            window.addEventListener('scroll', () => {
                if (raf) return;
                raf = requestAnimationFrame(() => {
                    update();
                    raf = null;
                });
            }, { passive: true });
            update();
        },
    };
});

Alpine.data('categoryScroll', () => ({
    scrollStep: 320,
    canScrollLeft: false,
    canScrollRight: true,
    totalDots: 1,
    currentDotIndex: 0,
    dots: [0],

    init() {
        this.$nextTick(() => this.updateButtons());
        window.addEventListener('resize', () => this.updateButtons());
    },

    updateButtons() {
        const el = this.$refs.categoryTrack;
        if (!el) return;
        const threshold = 2;
        const maxScroll = el.scrollWidth - el.clientWidth;
        this.canScrollLeft = el.scrollLeft > threshold;
        this.canScrollRight = maxScroll > threshold && el.scrollLeft < maxScroll - threshold;

        const total = maxScroll <= 0 ? 1 : Math.min(8, Math.ceil(maxScroll / this.scrollStep) + 1);
        if (total !== this.totalDots) {
            this.totalDots = total;
            this.dots = Array.from({ length: total }, (_, i) => i);
        }
        this.currentDotIndex = total <= 1 ? 0 : Math.min(total - 1, Math.round((el.scrollLeft / maxScroll) * (total - 1)));
    },

    scrollLeft() {
        const el = this.$refs.categoryTrack;
        if (el) el.scrollBy({ left: -this.scrollStep, behavior: 'smooth' });
    },

    scrollRight() {
        const el = this.$refs.categoryTrack;
        if (el) el.scrollBy({ left: this.scrollStep, behavior: 'smooth' });
    },

    scrollToDot(index) {
        const el = this.$refs.categoryTrack;
        if (!el || this.totalDots <= 1) return;
        const maxScroll = el.scrollWidth - el.clientWidth;
        const target = (index / (this.totalDots - 1)) * maxScroll;
        el.scrollTo({ left: target, behavior: 'smooth' });
    },
}));

// Trending now: on mobile, auto-scroll cards smoothly to the left
Alpine.data('trendingMobileAutoScroll', () => {
    let rafId = null;
    let lastTime = 0;
    const speedPxPerSec = 28;
    return {
        init() {
            this.$nextTick(() => {
                const el = this.$refs.trendingScroll;
                if (!el) return;
                const isMobile = () => window.innerWidth < 768;
                if (!isMobile()) return;

                const tick = (now) => {
                    if (!isMobile()) return;
                    const dt = (now - lastTime) / 1000;
                    lastTime = now;
                    const maxScroll = el.scrollWidth - el.clientWidth;
                    if (maxScroll <= 0) {
                        rafId = requestAnimationFrame(tick);
                        return;
                    }
                    el.scrollLeft += speedPxPerSec * dt;
                    if (el.scrollLeft >= maxScroll - 2) el.scrollLeft = 0;
                    rafId = requestAnimationFrame(tick);
                };

                const start = () => {
                    if (!isMobile() || document.hidden) return;
                    stop();
                    lastTime = performance.now();
                    rafId = requestAnimationFrame(tick);
                };
                const stop = () => {
                    if (rafId != null) {
                        cancelAnimationFrame(rafId);
                        rafId = null;
                    }
                };

                start();
                window.addEventListener('resize', () => {
                    if (!isMobile()) stop();
                    else start();
                });
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) stop();
                    else start();
                });
            });
        },
    };
});

// Shop page filters: must be registered before Alpine.start() so x-data="shopPage(...)" works
Alpine.data('shopPage', (priceMin, priceMax, currentMin, currentMax, initialCategory, initialSort, initialOccasions, initialRecipients, initialDelivery, initialRating, initialInStock, initialOnSale) => {
    let minP = currentMin !== '' && currentMin != null ? parseInt(currentMin, 10) : priceMin;
    let maxP = currentMax !== '' && currentMax != null ? parseInt(currentMax, 10) : priceMax;
    if (minP > maxP) { const t = minP; minP = maxP; maxP = t; }
    return {
        selectedCategory: initialCategory || '',
        sortBy: initialSort || 'latest',
        minPrice: minP,
        maxPrice: maxP,
        filterDrawerOpen: false,
        inStockOnly: !!initialInStock,

        submitFilterForm(formRef) {
            const form = this.$refs[formRef];
            if (!form) return;
            const minInput = form.querySelector('[name=min_price]');
            const maxInput = form.querySelector('[name=max_price]');
            const sortInput = form.querySelector('[name=sort]');
            const inStockInput = form.querySelector('[name=in_stock]');
            if (minInput) minInput.value = this.minPrice;
            if (maxInput) maxInput.value = this.maxPrice;
            if (sortInput) sortInput.value = this.sortBy;
            if (inStockInput) inStockInput.value = this.inStockOnly ? '1' : '';
            form.submit();
        },
        autoSubmit() {
            this.submitFilterForm('filterForm');
        },
        submitMobile() {
            this.submitFilterForm('filterFormMobile');
            this.filterDrawerOpen = false;
        },
        applyFiltersDebounced() {
            clearTimeout(this._applyTimeout);
            this._applyTimeout = setTimeout(() => this.autoSubmit(), 400);
        },
        applyFiltersDebouncedMobile() {
            clearTimeout(this._applyTimeoutMobile);
            this._applyTimeoutMobile = setTimeout(() => this.submitMobile(), 400);
        },
    };
});

const chatTimestampFormatter = new Intl.DateTimeFormat(undefined, {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
});

const formatChatTimestamp = (value) => {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return chatTimestampFormatter.format(date);
};

const formatChatFileSize = (value) => {
    const size = Number(value || 0);

    if (!size) {
        return '';
    }

    if (size < 1024 * 1024) {
        return `${Math.max(1, Math.round(size / 102.4) / 10)} KB`;
    }

    return `${Math.round((size / (1024 * 1024)) * 10) / 10} MB`;
};

const chatMessagePreview = (message) => {
    if (message?.body) {
        return message.body;
    }

    if (message?.attachment?.name) {
        return message.attachment.name;
    }

    return 'Attachment';
};

/** True when the chat UI should render an <img> preview (host-safe: also checks mime_type). */
const chatAttachmentIsImage = (message) => {
    const attachment = message?.attachment;
    if (!attachment) {
        return false;
    }
    if (attachment.is_image === true || attachment.is_image === 1) {
        return true;
    }
    const mime = String(attachment.mime_type ?? '');
    return mime.startsWith('image/');
};

const buildChatMessageFormData = (draft, attachmentFile) => {
    const formData = new FormData();
    const body = draft.trim();

    if (body) {
        formData.append('body', body);
    }

    if (attachmentFile) {
        formData.append('attachment', attachmentFile);
    }

    return formData;
};

const upsertChatMessage = (messages, incomingMessage) => {
    const nextMessages = Array.isArray(messages) ? [...messages] : [];
    const existingIndex = nextMessages.findIndex((message) => message.id === incomingMessage.id);

    if (existingIndex >= 0) {
        nextMessages.splice(existingIndex, 1, incomingMessage);
    } else {
        nextMessages.push(incomingMessage);
    }

    return nextMessages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
};

const upsertConversationSummary = (conversations, updatedConversation) => {
    const nextConversations = Array.isArray(conversations) ? [...conversations] : [];
    const existingIndex = nextConversations.findIndex((conversation) => conversation.id === updatedConversation.id);

    if (existingIndex >= 0) {
        nextConversations.splice(existingIndex, 1, updatedConversation);
    } else {
        nextConversations.unshift(updatedConversation);
    }

    return nextConversations.sort((a, b) => new Date(b.updated_at || 0) - new Date(a.updated_at || 0));
};

const subscribeToChatChannel = (state, channelName, callback) => {
    if (!window.Echo || !channelName) {
        return false;
    }

    if (state.channelName) {
        window.Echo.leave(state.channelName);
    }

    state.channelName = channelName;
    window.Echo.private(channelName).listen('.message.sent', (event) => {
        const incoming = event?.message ?? (event?.id && event?.conversation_id ? event : null);
        if (incoming) {
            callback(incoming);
        }
    });

    return true;
};

const leaveChatChannel = (state) => {
    if (window.Echo && state.channelName) {
        window.Echo.leave(state.channelName);
    }

    state.channelName = null;
};

Alpine.data('customerChatWidget', (config) => ({
    ...config,
    adminView: 'list',
    adminListFilter: 'all',
    open: false,
    loading: false,
    adminSummaryLoading: false,
    sending: false,
    error: '',
    draft: '',
    attachmentFile: null,
    attachmentName: '',
    conversation: null,
    messages: [],
    customerUnreadCount: 0,
    adminSummary: {
        new_messages: 0,
        unread_messages: 0,
        read_messages: 0,
        conversations: [],
    },
    channelName: null,
    pollTimer: null,
    customerBadgePollTimer: null,
    adminSummaryPollTimer: null,
    adminConversationPollTimer: null,
    pollingIntervalMs: 5000,
    broadcastingEnabled: false,

    init() {
        if (this.isAdminUser()) {
            this.loadAdminSummary();
            this.startAdminSummaryPolling();
        } else if (this.authenticated) {
            this.loadCustomerUnreadCount();
            this.startCustomerBadgePolling();
        }
    },

    canUseCustomerChat() {
        return !this.isAdminUser() && (this.authenticated || this.guestChatEnabled);
    },

    get supportProfile() {
        if (this.isAdminUser()) {
            if (this.adminView === 'chat' && this.conversation?.customer) {
                return {
                    name: this.conversation.customer.name || 'Customer Chat',
                    avatar_url: this.conversation.customer.avatar_url || null,
                    initials: this.conversation.customer.initials || 'C',
                };
            }

            return {
                name: 'Admin Inbox',
                avatar_url: this.currentUser?.avatar_url || null,
                initials: this.currentUser?.initials || 'A',
            };
        }

        const latestAdminMessage = [...this.messages]
            .reverse()
            .find((message) => message?.sender?.is_admin);

        if (latestAdminMessage?.sender) {
            return {
                name: latestAdminMessage.sender.name || this.defaultSupportProfile?.name || 'Customer Support',
                avatar_url: latestAdminMessage.sender.avatar_url || null,
                initials: latestAdminMessage.sender.initials || this.defaultSupportProfile?.initials || 'CS',
            };
        }

        return this.defaultSupportProfile || {
            name: 'Customer Support',
            avatar_url: null,
            initials: 'CS',
        };
    },

    isAdminUser() {
        return !!this.currentUser?.is_admin;
    },
    
    /** Welcome note after the customer starts the chat; hidden once an admin has replied. */
    shouldShowCustomerWelcome() {
        if (this.isAdminUser() || !this.canUseCustomerChat() || !Array.isArray(this.messages) || this.messages.length === 0) {
            return false;
        }

        return !this.messages.some((m) => m?.sender?.is_admin);
    },

    canSendMessage() {
        return !!this.draft.trim() || !!this.attachmentFile;
    },

    messagePreview(message) {
        return chatMessagePreview(message);
    },

    formatAttachmentSize(value) {
        return formatChatFileSize(value);
    },

    attachmentShowsImagePreview(message) {
        return chatAttachmentIsImage(message);
    },

    openAttachmentPicker(refName) {
        this.$refs[refName]?.click();
    },

    handleAttachmentSelection(event) {
        const [file] = Array.from(event?.target?.files || []);

        if (!file) {
            this.attachmentFile = null;
            this.attachmentName = '';
            return;
        }

        this.attachmentFile = file;
        this.attachmentName = file.name;
    },

    clearAttachment(refName) {
        this.attachmentFile = null;
        this.attachmentName = '';

        if (this.$refs[refName]) {
            this.$refs[refName].value = '';
        }
    },

    get filteredAdminConversations() {
        const conversations = Array.isArray(this.adminSummary?.conversations)
            ? this.adminSummary.conversations
            : [];

        if (this.adminListFilter === 'unread') {
            return conversations.filter((conversation) => (conversation.unread_messages_count || 0) > 0);
        }

        return conversations;
    },

    togglePanel() {
        if (this.open) {
            this.closePanel();
            return;
        }

        this.open = true;

        if (this.isAdminUser()) {
            this.adminView = 'list';
            this.adminListFilter = 'all';
            this.loadAdminSummary();
        } else if (this.canUseCustomerChat()) {
            this.loadConversation();
        }
    },

    closePanel() {
        this.open = false;
        this.adminView = 'list';
        this.conversation = null;
        this.messages = [];
        this.draft = '';
        this.customerUnreadCount = 0;
        this.clearAttachment('customerAttachmentInput');
        this.clearAttachment('adminAttachmentInput');
        this.stopPolling();
        this.stopAdminConversationPolling();
        leaveChatChannel(this);

        if (this.authenticated && !this.isAdminUser()) {
            this.loadCustomerUnreadCount();
        }
    },

    async loadCustomerUnreadCount() {
        if (!this.authenticated || this.isAdminUser() || !this.endpoints?.conversation || this.open) {
            return;
        }

        try {
            const { data } = await window.axios.get(this.endpoints.conversation, {
                params: { mark_read: false },
                headers: { Accept: 'application/json' },
            });

            this.customerUnreadCount = Number(data?.unread_messages_count || 0);
        } catch (error) {
            // Keep the last known badge count if the refresh fails.
        }
    },

    startCustomerBadgePolling() {
        if (!this.authenticated || this.isAdminUser()) {
            return;
        }

        this.stopCustomerBadgePolling();
        this.customerBadgePollTimer = window.setInterval(() => {
            if (document.hidden) return;
            this.loadCustomerUnreadCount();
        }, Math.max(this.pollingIntervalMs, 20000));
    },

    stopCustomerBadgePolling() {
        if (this.customerBadgePollTimer) {
            window.clearInterval(this.customerBadgePollTimer);
            this.customerBadgePollTimer = null;
        }
    },

    async loadAdminSummary() {
        if (!this.isAdminUser() || this.adminSummaryLoading || !this.endpoints?.adminSummary) {
            return;
        }

        this.adminSummaryLoading = true;
        this.error = '';

        try {
            const { data } = await window.axios.get(this.endpoints.adminSummary, {
                headers: { Accept: 'application/json' },
            });

            this.adminSummary = {
                new_messages: data?.stats?.new_messages || 0,
                unread_messages: data?.stats?.unread_messages || 0,
                read_messages: data?.stats?.read_messages || 0,
                conversations: Array.isArray(data?.conversations) ? data.conversations : [],
            };
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to load admin message stats right now.';
        } finally {
            this.adminSummaryLoading = false;
        }
    },

    startAdminSummaryPolling() {
        if (!this.isAdminUser()) {
            return;
        }

        this.stopAdminSummaryPolling();
        this.adminSummaryPollTimer = window.setInterval(() => {
            if (document.hidden) return;
            this.loadAdminSummary();
        }, Math.max(this.pollingIntervalMs, 15000));
    },

    stopAdminSummaryPolling() {
        if (this.adminSummaryPollTimer) {
            window.clearInterval(this.adminSummaryPollTimer);
            this.adminSummaryPollTimer = null;
        }
    },

    async openAdminConversation(conversationId) {
        if (!this.isAdminUser() || this.loading || !conversationId || !this.endpoints?.adminChatBase) {
            return;
        }

        this.loading = true;
        this.error = '';

        try {
            const { data } = await window.axios.get(`${this.endpoints.adminChatBase}/${conversationId}`, {
                headers: { Accept: 'application/json' },
            });

            this.adminView = 'chat';
            this.conversation = data.conversation;
            this.messages = Array.isArray(data.messages) ? data.messages : [];
            this.pollingIntervalMs = data.polling_interval_ms || this.pollingIntervalMs;
            this.broadcastingEnabled = !!data.broadcasting?.enabled;
            this.setupAdminConversationUpdates(conversationId, data.channel);
            await this.loadAdminSummary();
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to open this conversation.';
        } finally {
            this.loading = false;
        }
    },

    backToAdminList() {
        this.adminView = 'list';
        this.conversation = null;
        this.messages = [];
        this.draft = '';
        this.error = '';
        this.clearAttachment('adminAttachmentInput');
        this.stopAdminConversationPolling();
        leaveChatChannel(this);
    },

    setupAdminConversationUpdates(conversationId, channelName) {
        this.stopAdminConversationPolling();

        const subscribed = this.broadcastingEnabled && subscribeToChatChannel(this, channelName, async (message) => {
            this.messages = upsertChatMessage(this.messages, message);
            await this.loadAdminSummary();
            this.$nextTick(() => this.scrollToBottom());
        });

        if (!subscribed) {
            this.startAdminConversationPolling(conversationId);
        }
    },

    startAdminConversationPolling(conversationId) {
        this.stopAdminConversationPolling();

        if (!this.isAdminUser() || !this.open || this.adminView !== 'chat' || !conversationId) {
            return;
        }

        this.adminConversationPollTimer = window.setInterval(() => {
            if (document.hidden) return;
            this.openAdminConversation(conversationId);
        }, this.pollingIntervalMs);
    },

    stopAdminConversationPolling() {
        if (this.adminConversationPollTimer) {
            window.clearInterval(this.adminConversationPollTimer);
            this.adminConversationPollTimer = null;
        }
    },

    async loadConversation() {
        if (!this.canUseCustomerChat() || this.loading || !this.endpoints?.conversation) {
            return;
        }

        this.loading = true;
        this.error = '';

        try {
            const { data } = await window.axios.get(this.endpoints.conversation, {
                headers: { Accept: 'application/json' },
            });

            this.applyConversationPayload(data);
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to load chat right now.';
        } finally {
            this.loading = false;
        }
    },

    applyConversationPayload(payload) {
        this.conversation = payload.conversation;
        const serverMessages = Array.isArray(payload.messages) ? payload.messages : [];
        this.messages = this.applyGuestAutoMessage(serverMessages);
        this.customerUnreadCount = Number(payload?.unread_messages_count || 0);
        this.pollingIntervalMs = payload.polling_interval_ms || this.pollingIntervalMs;
        this.broadcastingEnabled = !!payload.broadcasting?.enabled;
        this.setupLiveUpdates(payload.channel);
        this.$nextTick(() => this.scrollToBottom());
    },

    applyGuestAutoMessage(messages) {
        const normalized = Array.isArray(messages) ? messages : [];

        if (this.authenticated || !this.guestChatEnabled || normalized.length > 0) {
            return normalized;
        }

        const note = String(this.guestAutoNote || '').trim();

        if (!note) {
            return normalized;
        }

        return [{
            id: 'guest-auto-note',
            conversation_id: this.conversation?.id ?? null,
            body: note,
            attachment: null,
            read_at: null,
            created_at: new Date().toISOString(),
            sender: {
                id: null,
                name: this.defaultSupportProfile?.name || 'Customer Support',
                email: null,
                avatar_url: this.defaultSupportProfile?.avatar_url || null,
                initials: this.defaultSupportProfile?.initials || 'CS',
                is_admin: true,
            },
        }];
    },

    setupLiveUpdates(channelName) {
        this.stopPolling();

        const subscribed = this.broadcastingEnabled && subscribeToChatChannel(this, channelName, (message) => {
            this.messages = upsertChatMessage(this.messages, message);

            if (message?.sender?.is_admin) {
                this.customerUnreadCount = 0;
            }

            this.$nextTick(() => this.scrollToBottom());
        });

        if (!subscribed) {
            this.startPolling();
        }
    },

    startPolling() {
        this.stopPolling();

        if (!this.canUseCustomerChat() || !this.open || !this.endpoints?.conversation) {
            return;
        }

        this.pollTimer = window.setInterval(async () => {
            if (document.hidden) return;
            try {
                const { data } = await window.axios.get(this.endpoints.conversation, {
                    headers: { Accept: 'application/json' },
                });

                this.conversation = data.conversation;
                this.messages = this.applyGuestAutoMessage(Array.isArray(data.messages) ? data.messages : this.messages);
            } catch (error) {
                // Keep the current state when fallback polling fails.
            }
        }, this.pollingIntervalMs);
    },

    stopPolling() {
        if (this.pollTimer) {
            window.clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    },

    async sendMessage() {
        if (!this.canUseCustomerChat() || this.sending || !this.canSendMessage()) {
            return;
        }

        this.sending = true;
        this.error = '';

        try {
            const { data } = await window.axios.post(this.endpoints.sendMessage, buildChatMessageFormData(this.draft, this.attachmentFile), {
                headers: { Accept: 'application/json' },
            });

            this.messages = upsertChatMessage(this.messages, data.message);
            this.conversation = data.conversation;
            this.draft = '';
            this.clearAttachment('customerAttachmentInput');
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to send your message.';
        } finally {
            this.sending = false;
        }
    },

    async sendAdminReply() {
        if (!this.isAdminUser() || this.sending || !this.canSendMessage() || !this.conversation?.id || !this.endpoints?.adminChatBase) {
            return;
        }

        this.sending = true;
        this.error = '';

        try {
            const { data } = await window.axios.post(`${this.endpoints.adminChatBase}/${this.conversation.id}/messages`, buildChatMessageFormData(this.draft, this.attachmentFile), {
                headers: { Accept: 'application/json' },
            });

            this.messages = upsertChatMessage(this.messages, data.message);
            this.conversation = data.conversation;
            this.draft = '';
            this.clearAttachment('adminAttachmentInput');
            await this.loadAdminSummary();
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to send reply.';
        } finally {
            this.sending = false;
        }
    },

    isOwnMessage(message) {
        return message?.sender?.id === this.currentUser?.id;
    },

    formatTimestamp(value) {
        return formatChatTimestamp(value);
    },

    scrollToBottom() {
        const container = this.$refs.adminMessagesContainer || this.$refs.messagesContainer;

        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    },
}));

Alpine.data('adminChatPanel', (config) => ({
    ...config,
    conversations: config.initialConversations || [],
    selectedConversation: config.initialConversation || null,
    messages: config.initialMessages || [],
    search: '',
    draft: '',
    attachmentFile: null,
    attachmentName: '',
    error: '',
    sending: false,
    conversationLoading: false,
    channelName: null,
    listPollTimer: null,
    conversationPollTimer: null,

    init() {
        this.startListPolling();

        if (this.selectedConversation) {
            this.setupConversationUpdates(this.selectedConversation.id, this.channelNameFor(this.selectedConversation.id));
            this.$nextTick(() => this.scrollToBottom());
        }
    },

    get filteredConversations() {
        const term = this.search.trim().toLowerCase();

        if (!term) {
            return this.conversations;
        }

        return this.conversations.filter((conversation) => {
            const haystack = [
                conversation.customer?.name,
                conversation.customer?.email,
                conversation.latest_message?.body,
                conversation.latest_message?.attachment?.name,
            ].join(' ').toLowerCase();

            return haystack.includes(term);
        });
    },

    channelNameFor(conversationId) {
        return conversationId ? `chat.conversation.${conversationId}` : null;
    },

    canSendMessage() {
        return !!this.draft.trim() || !!this.attachmentFile;
    },

    messagePreview(message) {
        return chatMessagePreview(message);
    },

    formatAttachmentSize(value) {
        return formatChatFileSize(value);
    },

    attachmentShowsImagePreview(message) {
        return chatAttachmentIsImage(message);
    },

    openAttachmentPicker() {
        this.$refs.adminAttachmentInput?.click();
    },

    handleAttachmentSelection(event) {
        const [file] = Array.from(event?.target?.files || []);

        if (!file) {
            this.attachmentFile = null;
            this.attachmentName = '';
            return;
        }

        this.attachmentFile = file;
        this.attachmentName = file.name;
    },

    clearAttachment() {
        this.attachmentFile = null;
        this.attachmentName = '';

        if (this.$refs.adminAttachmentInput) {
            this.$refs.adminAttachmentInput.value = '';
        }
    },

    async refreshConversationList() {
        try {
            const { data } = await window.axios.get(this.chatIndexUrl, {
                headers: { Accept: 'application/json' },
            });

            this.conversations = Array.isArray(data.conversations) ? data.conversations : this.conversations;

            if (this.selectedConversation) {
                const updatedSelected = this.conversations.find((conversation) => conversation.id === this.selectedConversation.id);

                if (updatedSelected) {
                    this.selectedConversation = updatedSelected;
                }
            }
        } catch (error) {
            // Keep rendering the last known inbox list if refresh fails.
        }
    },

    startListPolling() {
        this.stopListPolling();
        this.listPollTimer = window.setInterval(() => { if (!document.hidden) this.refreshConversationList(); }, this.pollingIntervalMs || 5000);
    },

    stopListPolling() {
        if (this.listPollTimer) {
            window.clearInterval(this.listPollTimer);
            this.listPollTimer = null;
        }
    },

    async selectConversation(conversationId, { preserveUrl = false } = {}) {
        if (!conversationId) {
            return;
        }

        this.conversationLoading = true;
        this.error = '';

        try {
            const { data } = await window.axios.get(`${this.chatBaseUrl}/${conversationId}`, {
                headers: { Accept: 'application/json' },
            });

            this.selectedConversation = data.conversation;
            this.messages = Array.isArray(data.messages) ? data.messages : [];
            this.conversations = upsertConversationSummary(this.conversations, data.conversation);
            this.setupConversationUpdates(conversationId, data.channel);
            if (!preserveUrl) {
                window.history.replaceState({}, '', `${this.chatIndexUrl}?conversation=${conversationId}`);
            }
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to load this conversation.';
        } finally {
            this.conversationLoading = false;
        }
    },

    setupConversationUpdates(conversationId, channelName) {
        this.stopConversationPolling();

        const subscribed = this.broadcastingEnabled && subscribeToChatChannel(this, channelName, (message) => {
            this.messages = upsertChatMessage(this.messages, message);

            const latestConversation = {
                ...(this.selectedConversation || {}),
                latest_message: message,
                updated_at: message.created_at,
            };

            this.selectedConversation = latestConversation;
            this.conversations = upsertConversationSummary(this.conversations, latestConversation);
            this.$nextTick(() => this.scrollToBottom());
        });

        if (!subscribed && conversationId) {
            this.conversationPollTimer = window.setInterval(() => {
                if (document.hidden) return;
                this.selectConversation(conversationId, { preserveUrl: true });
            }, this.pollingIntervalMs || 5000);
        }
    },

    stopConversationPolling() {
        if (this.conversationPollTimer) {
            window.clearInterval(this.conversationPollTimer);
            this.conversationPollTimer = null;
        }
    },

    async sendMessage() {
        if (this.sending || !this.selectedConversation || !this.canSendMessage()) {
            return;
        }

        this.sending = true;
        this.error = '';

        try {
            const { data } = await window.axios.post(`${this.chatBaseUrl}/${this.selectedConversation.id}/messages`, buildChatMessageFormData(this.draft, this.attachmentFile), {
                headers: { Accept: 'application/json' },
            });

            this.messages = upsertChatMessage(this.messages, data.message);
            this.selectedConversation = data.conversation;
            this.conversations = upsertConversationSummary(this.conversations, data.conversation);
            this.draft = '';
            this.clearAttachment();
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.response?.data?.message || 'Unable to send reply.';
        } finally {
            this.sending = false;
        }
    },

    isOwnMessage(message) {
        return message?.sender?.id === this.currentUser?.id;
    },

    formatTimestamp(value) {
        return formatChatTimestamp(value);
    },

    scrollToBottom() {
        const container = this.$refs.adminMessagesContainer;

        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    },
}));

Alpine.start();
