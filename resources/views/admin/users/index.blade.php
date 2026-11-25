@extends('layouts.admin')

@section('title', 'Users & Admins')

@section('content')
@php
    $sel = 'w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50/50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none';
@endphp
<div class="space-y-6" x-data="{ modalOpen: false, userId: null, userName: '', newRole: '', selected: [] }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-bold text-stone-800 dark:text-stone-100">Users &amp; Admins</h1>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Grant or revoke admin access. {{ $adminCount }} admin{{ $adminCount === 1 ? '' : 's' }}, {{ $blockedCount }} blocked.</p>
        </div>
        <a href="{{ route('admin.users.export') }}" class="inline-flex items-center rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 px-4 py-2.5 text-sm font-medium text-stone-700 dark:text-stone-200 shadow-sm hover:bg-stone-50">Export CSV</a>
    </div>

    <form action="{{ route('admin.users.index') }}" method="GET" class="admin-card rounded-2xl border border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-4 shadow-soft">
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-6">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search name or email..." class="{{ $sel }} col-span-2">
            <select name="role" class="{{ $sel }}">
                <option value="">All roles</option>
                <option value="admin" @selected($roleFilter === 'admin')>Admins</option>
                <option value="customer" @selected($roleFilter === 'customer')>Customers</option>
            </select>
            <select name="status" class="{{ $sel }}">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
            </select>
            <select name="orders" class="{{ $sel }}">
                <option value="">Any orders</option>
                <option value="with" @selected(request('orders') === 'with')>Has orders</option>
                <option value="none" @selected(request('orders') === 'none')>No orders</option>
            </select>
            <select name="sort" class="{{ $sel }}">
                <option value="name" @selected($sort === 'name')>Sort: Name</option>
                <option value="newest" @selected($sort === 'newest')>Sort: Newest</option>
                <option value="orders" @selected($sort === 'orders')>Sort: Most orders</option>
                <option value="spent" @selected($sort === 'spent')>Sort: Top spenders</option>
                <option value="last_login" @selected($sort === 'last_login')>Sort: Last login</option>
            </select>
        </div>
        <div class="mt-3 flex gap-2">
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-stone-200 dark:border-stone-600 px-4 py-2 text-sm text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-700">Reset</a>
        </div>
    </form>

    {{-- Bulk actions (checkboxes below are linked to this form via the form attribute) --}}
    <form id="bulk-form" method="POST" action="{{ route('admin.users.bulk') }}" x-show="selected.length > 0" x-cloak
        class="admin-card flex flex-col gap-3 rounded-2xl border border-primary-200 bg-primary-50/60 dark:bg-stone-800 p-4 sm:flex-row sm:items-center">
        @csrf
        <span class="text-sm font-medium text-stone-700 dark:text-stone-200"><span x-text="selected.length"></span> selected</span>
        <input type="text" name="reason" maxlength="255" placeholder="Reason (for blocking, optional)" class="{{ $sel }} sm:max-w-xs">
        <div class="flex gap-2">
            <button type="submit" name="action" value="block" onclick="return confirm('Block the selected users?')" class="rounded-xl bg-danger-600 px-4 py-2 text-xs font-semibold text-white hover:bg-danger-700">Block selected</button>
            <button type="submit" name="action" value="unblock" onclick="return confirm('Unblock the selected users?')" class="rounded-xl border border-stone-300 px-4 py-2 text-xs font-semibold text-stone-700 dark:text-stone-200 hover:bg-white">Unblock selected</button>
        </div>
    </form>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                <thead class="bg-stone-50/80 dark:bg-stone-900/50">
                    <tr>
                        <th class="w-10 px-4 py-3"></th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">User</th>
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Orders</th>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Last login</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Role</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                    @forelse($users as $user)
                        @php $isAdminUser = $user->isAdmin(); $isSelf = $user->id === auth()->id(); @endphp
                        <tr class="hover:bg-stone-50/50 dark:hover:bg-stone-700/30 transition-colors">
                            <td class="px-4 py-4">
                                @unless($isAdminUser || $isSelf)
                                    <input type="checkbox" name="ids[]" value="{{ $user->id }}" form="bulk-form" x-model="selected" class="rounded border-stone-300 text-primary-600">
                                @endunless
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm">
                                <div class="font-medium text-stone-800 dark:text-stone-100"><a href="{{ route('admin.users.show', $user) }}" class="hover:text-primary-600">{{ $user->name }}</a> @if($isSelf)<span class="text-xs text-stone-400">(you)</span>@endif</div>
                                <div class="text-stone-500 dark:text-stone-400 break-all">{{ $user->email }}</div>
                            </td>
                            <td class="hidden md:table-cell whitespace-nowrap px-6 py-4 text-sm text-stone-600 dark:text-stone-300">
                                {{ $user->orders_count }} <span class="text-xs text-stone-400">&middot; ₨ {{ number_format((float) $user->spent, 0) }}</span>
                            </td>
                            <td class="hidden lg:table-cell whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isAdminUser ? 'bg-primary-100 text-primary-800' : 'bg-stone-100 text-stone-700' }}">{{ ucfirst($user->role?->name ?? 'none') }}</span>
                                @if($user->isBlocked())<span class="ml-1 inline-flex rounded-full bg-danger-100 px-2.5 py-0.5 text-xs font-semibold text-danger-700">Blocked</span>@endif
                            </td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="mr-2 text-xs font-medium text-primary-600 hover:text-primary-700">View</a>
                                @if($isAdminUser && $isSelf)
                                    <span class="text-xs text-stone-400">You</span>
                                @elseif($isAdminUser)
                                    <button type="button" @click="userId = {{ $user->id }}; userName = @js($user->name); newRole = 'customer'; modalOpen = true"
                                        class="rounded-lg border border-danger-200 px-3 py-1.5 text-xs font-semibold text-danger-700 hover:bg-danger-50">Revoke admin</button>
                                @else
                                    <button type="button" @click="userId = {{ $user->id }}; userName = @js($user->name); newRole = 'admin'; modalOpen = true"
                                        class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">Make admin</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-stone-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 dark:border-stone-700 px-4 sm:px-6 py-3">{{ $users->links() }}</div>
    </div>

    <div x-show="modalOpen" x-cloak @keydown.escape.window="modalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
        <div @click.outside="modalOpen = false" class="w-full max-w-md rounded-2xl bg-white dark:bg-stone-800 p-6 shadow-xl">
            <h2 class="font-display text-lg font-bold text-stone-800 dark:text-stone-100" x-text="newRole === 'admin' ? 'Grant admin access?' : 'Revoke admin access?'"></h2>
            <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                <span x-show="newRole === 'admin'">This gives <strong x-text="userName"></strong> full access to the admin panel.</span>
                <span x-show="newRole === 'customer'"><strong x-text="userName"></strong> will lose admin access and become a customer.</span>
            </p>
            <form :action="'{{ url('admin/users') }}/' + userId + '/role'" method="POST" class="mt-5 flex justify-end gap-2">
                @csrf
                @method('PATCH')
                <input type="hidden" name="role" :value="newRole">
                <button type="button" @click="modalOpen = false" class="rounded-xl border border-stone-200 dark:border-stone-600 px-4 py-2 text-xs font-medium text-stone-600 dark:text-stone-300">Cancel</button>
                <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection
