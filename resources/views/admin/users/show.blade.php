@extends('layouts.admin')

@section('title', $user->name)

@section('content')
@php $isSelf = $user->id === auth()->id(); @endphp
<div class="space-y-6" x-data="{ blockOpen: false }">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-800 dark:hover:text-stone-200">&larr; Back to users</a>

    <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                @if($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="" class="h-14 w-14 rounded-full object-cover">
                @else
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary-100 text-lg font-bold text-primary-700">{{ $user->initials() }}</div>
                @endif
                <div class="min-w-0">
                    <h1 class="font-display text-xl font-bold text-stone-800 dark:text-stone-100 truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-stone-500 break-all">{{ $user->email }}</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $user->isAdmin() ? 'bg-primary-100 text-primary-800' : 'bg-stone-100 text-stone-700' }}">{{ ucfirst($user->role?->name ?? 'none') }}</span>
                        @if($user->isBlocked())<span class="rounded-full bg-danger-100 px-2.5 py-0.5 text-xs font-semibold text-danger-700">Blocked</span>@endif
                        @if($user->email_verified_at)<span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs text-stone-600">Email verified</span>@endif
                        @if($user->google_id)<span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs text-stone-600">Google</span>@endif
                    </div>
                </div>
            </div>

            <div class="shrink-0">
                @if($isSelf)
                    <span class="text-xs text-stone-400">This is you</span>
                @elseif($user->isBlocked())
                    <form method="POST" action="{{ route('admin.users.unblock', $user) }}" onsubmit="return confirm('Unblock this user?')">
                        @csrf
                        <button class="w-full sm:w-auto rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Unblock user</button>
                    </form>
                @elseif($user->isAdmin())
                    <span class="text-xs text-stone-400">Revoke admin access before blocking</span>
                @else
                    <button type="button" @click="blockOpen = true" class="w-full sm:w-auto rounded-xl border border-danger-200 px-4 py-2 text-sm font-semibold text-danger-700 hover:bg-danger-50">Block user</button>
                @endif
            </div>
        </div>

        @if($user->isBlocked())
            <div class="mt-4 rounded-xl border border-danger-200 bg-danger-50 dark:bg-danger-900/30 px-4 py-3 text-sm text-danger-800 dark:text-danger-200">
                Blocked {{ $user->blocked_at->format('M d, Y h:i A') }}@if($user->blocked_reason) &mdash; {{ $user->blocked_reason }}@endif
            </div>
        @endif

        <dl class="mt-5 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="text-xs uppercase text-stone-400">Phone</dt><dd class="text-stone-800 dark:text-stone-100">{{ $user->phone ?: '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-stone-400">District</dt><dd class="text-stone-800 dark:text-stone-100">{{ $user->district ?: '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-stone-400">Address</dt><dd class="text-stone-800 dark:text-stone-100">{{ $user->address ?: '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-stone-400">Last login</dt><dd class="text-stone-800 dark:text-stone-100">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') . ' (' . $user->last_login_ip . ')' : 'Never' }}</dd></div>
            <div><dt class="text-xs uppercase text-stone-400">Joined</dt><dd class="text-stone-800 dark:text-stone-100">{{ $user->created_at?->format('M d, Y') }}</dd></div>
        </dl>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([['Orders', $stats['orders']], ['Total paid', '₨ ' . number_format($stats['spent'], 2)], ['Cancelled', $stats['cancelled']], ['Last order', $stats['last_order'] ? \Carbon\Carbon::parse($stats['last_order'])->format('M d, Y') : '—']] as [$label, $value])
            <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-4">
                <p class="text-xs uppercase tracking-wide text-stone-400">{{ $label }}</p>
                <p class="mt-1 text-lg font-bold text-stone-800 dark:text-stone-100">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
        <div class="border-b border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3 font-semibold text-stone-800 dark:text-stone-100">Orders</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                <thead class="bg-stone-50/80 dark:bg-stone-900/50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase text-stone-500">Order</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase text-stone-500">Date</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase text-stone-500">Total</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase text-stone-500">Payment</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase text-stone-500">Status</th>
                        <th class="px-4 sm:px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                    @forelse($orders as $order)
                        <tr>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-sm font-medium text-stone-800 dark:text-stone-100">#{{ $order->id }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-sm text-stone-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-sm">₨ {{ number_format($order->total_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-sm text-stone-600">{{ ucfirst($order->payment_status ?? '—') }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-sm text-stone-600">{{ ucfirst($order->status) }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3 text-right text-sm"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 hover:text-primary-700">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-stone-500">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3">{{ $orders->links() }}</div>
    </div>

    {{-- Account actions --}}
    <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-4 sm:p-5">
        <h2 class="mb-3 font-semibold text-stone-800 dark:text-stone-100">Account actions</h2>
        <div class="flex flex-col gap-2 sm:flex-row">
            <form method="POST" action="{{ route('admin.users.passwordReset', $user) }}" onsubmit="return confirm('Email a password reset link to this user?')">
                @csrf
                <button class="w-full rounded-xl border border-stone-200 dark:border-stone-600 px-4 py-2 text-sm font-medium text-stone-700 dark:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-700">Send password reset link</button>
            </form>
            @unless($user->email_verified_at)
                <form method="POST" action="{{ route('admin.users.resendVerification', $user) }}">
                    @csrf
                    <button class="w-full rounded-xl border border-stone-200 dark:border-stone-600 px-4 py-2 text-sm font-medium text-stone-700 dark:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-700">Resend verification email</button>
                </form>
            @endunless
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
            <div class="border-b border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3 font-semibold text-stone-800 dark:text-stone-100">Wishlist ({{ $wishlist->count() }})</div>
            <ul class="divide-y divide-stone-100 dark:divide-stone-700 text-sm">
                @forelse($wishlist as $w)
                    <li class="px-4 sm:px-6 py-2.5 text-stone-700 dark:text-stone-200">{{ $w->product?->name ?? 'Deleted product' }}</li>
                @empty
                    <li class="px-6 py-6 text-center text-stone-500">Nothing in wishlist.</li>
                @endforelse
            </ul>
        </div>

        <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
            <div class="border-b border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3 font-semibold text-stone-800 dark:text-stone-100">Reviews ({{ $reviews->count() }})</div>
            <ul class="divide-y divide-stone-100 dark:divide-stone-700 text-sm">
                @forelse($reviews as $r)
                    <li class="px-4 sm:px-6 py-2.5">
                        <div class="text-stone-800 dark:text-stone-100">{{ $r->product?->name ?? 'Deleted product' }} <span class="text-amber-500">{{ str_repeat('★', (int) $r->rating) }}</span></div>
                        @if($r->comment)<div class="text-stone-500">{{ \Illuminate\Support\Str::limit($r->comment, 160) }}</div>@endif
                    </li>
                @empty
                    <li class="px-6 py-6 text-center text-stone-500">No reviews.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 px-4 sm:px-6 py-3 text-sm text-stone-700 dark:text-stone-200">
        <span class="font-semibold">Chat:</span>
        @if($conversation)
            {{ $conversation->messages_count }} message(s) &mdash; <a href="{{ route('admin.chats.show', $conversation) }}" class="text-primary-600 hover:text-primary-700 font-medium">Open conversation</a>
        @else
            No chat history.
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
            <div class="border-b border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3 font-semibold text-stone-800 dark:text-stone-100">Internal notes</div>
            <form method="POST" action="{{ route('admin.users.notes.store', $user) }}" class="space-y-2 p-4 sm:px-6">
                @csrf
                <textarea name="body" rows="2" maxlength="2000" required placeholder="Add a private note (only admins see this)" class="w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-900 text-stone-800 dark:text-stone-100 p-2.5 text-sm"></textarea>
                <button class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700">Add note</button>
            </form>
            <ul class="divide-y divide-stone-100 dark:divide-stone-700 text-sm">
                @foreach($notes as $note)
                    <li class="flex items-start justify-between gap-3 px-4 sm:px-6 py-3">
                        <div class="min-w-0">
                            <p class="whitespace-pre-line break-words text-stone-800 dark:text-stone-100">{{ $note->body }}</p>
                            <p class="mt-1 text-xs text-stone-400">{{ $note->admin?->name ?? 'Deleted admin' }} &middot; {{ $note->created_at->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.notes.destroy', [$user, $note]) }}" onsubmit="return confirm('Delete this note?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-danger-600 hover:text-danger-700">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="admin-card rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
            <div class="border-b border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3 font-semibold text-stone-800 dark:text-stone-100">Admin activity log</div>
            <ul class="divide-y divide-stone-100 dark:divide-stone-700 text-sm">
                @forelse($logs as $log)
                    <li class="px-4 sm:px-6 py-2.5">
                        <div class="text-stone-800 dark:text-stone-100">{{ ucfirst(str_replace('_', ' ', $log->action)) }}@if($log->details) <span class="text-stone-500">&mdash; {{ $log->details }}</span>@endif</div>
                        <div class="text-xs text-stone-400">by {{ $log->admin?->name ?? 'Unknown' }} &middot; {{ $log->created_at->format('M d, Y h:i A') }}@if($log->ip) &middot; {{ $log->ip }}@endif</div>
                    </li>
                @empty
                    <li class="px-6 py-6 text-center text-stone-500">No admin activity yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div x-show="blockOpen" x-cloak @keydown.escape.window="blockOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
        <div @click.outside="blockOpen = false" class="w-full max-w-md rounded-2xl bg-white dark:bg-stone-800 p-6 shadow-xl">
            <h2 class="font-display text-lg font-bold text-stone-800 dark:text-stone-100">Block {{ $user->name }}?</h2>
            <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">They will be signed out and unable to log in until unblocked.</p>
            <form method="POST" action="{{ route('admin.users.block', $user) }}" class="mt-4 space-y-4">
                @csrf
                <input type="text" name="reason" maxlength="255" placeholder="Reason (optional)" class="w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-900 text-stone-800 dark:text-stone-100 p-2.5 text-sm">
                <label class="flex items-center gap-2 text-sm text-stone-600 dark:text-stone-300"><input type="checkbox" name="notify" value="1" class="rounded border-stone-300"> Email the user about this</label>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="blockOpen = false" class="rounded-xl border border-stone-200 dark:border-stone-600 px-4 py-2 text-xs font-medium text-stone-600 dark:text-stone-300">Cancel</button>
                    <button type="submit" class="rounded-xl bg-danger-600 px-4 py-2 text-xs font-semibold text-white hover:bg-danger-700">Block</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
