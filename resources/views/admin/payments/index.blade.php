@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, activeOrder: null, targetStatus: 'paid', txId: '' }">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between max-md:gap-3">
        <div>
            <h1 class="font-display text-2xl max-md:text-xl font-bold text-stone-800 dark:text-stone-100">Payments Management</h1>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Track online card transactions, verify bank transfers, and audit payment records.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payments.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 px-4 py-2.5 text-sm font-medium text-stone-700 dark:text-stone-200 shadow-sm transition hover:bg-stone-50 dark:hover:bg-stone-700/60">
                <svg class="h-4 w-4 text-stone-400 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Paid --}}
        <div class="admin-card rounded-2xl border border-stone-200/80 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Collected</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold text-stone-900 dark:text-stone-100">₨ {{ number_format($totalPaid, 2) }}</p>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">All settled payments</p>
        </div>

        {{-- Online / Card Paid --}}
        <div class="admin-card rounded-2xl border border-stone-200/80 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">Card (PayHere)</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold text-stone-900 dark:text-stone-100">₨ {{ number_format($cardPaid, 2) }}</p>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">Online gateway revenue</p>
        </div>

        {{-- Bank Transfer Paid --}}
        <div class="admin-card rounded-2xl border border-stone-200/80 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">Bank Transfer</span>
                <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold text-stone-900 dark:text-stone-100">₨ {{ number_format($bankPaid, 2) }}</p>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">Direct deposit & slips</p>
        </div>

        {{-- Pending Payments --}}
        <div class="admin-card rounded-2xl border border-stone-200/80 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">Pending</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingCount }} orders</p>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">₨ {{ number_format($pendingAmount, 2) }} awaiting confirmation</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form action="{{ route('admin.payments.index') }}" method="GET" class="admin-card rounded-2xl border border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-4 shadow-soft">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search input --}}
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-stone-600 dark:text-stone-300 mb-1">Search</label>
                <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Order ID, Customer, Phone, Transaction ID..."
                       class="w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50/50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none">
            </div>

            {{-- Method Filter --}}
            <div>
                <label for="method" class="block text-xs font-semibold text-stone-600 dark:text-stone-300 mb-1">Method</label>
                <select id="method" name="method" class="w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50/50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">All Methods</option>
                    <option value="online" {{ request('method') === 'online' ? 'selected' : '' }}>Card (PayHere)</option>
                    <option value="cash_on_delivery" {{ request('method') === 'cash_on_delivery' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <label for="status" class="block text-xs font-semibold text-stone-600 dark:text-stone-300 mb-1">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50/50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition">Filter</button>
                @if(request()->hasAny(['search', 'method', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.payments.index') }}" class="rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 px-3 py-2 text-sm text-stone-600 dark:text-stone-300 hover:bg-stone-50">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Payments Table --}}
    <div class="admin-card overflow-hidden rounded-2xl border border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-800/90 shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700 text-sm">
                <thead class="bg-stone-50 dark:bg-stone-800/80 text-xs uppercase font-semibold text-stone-500 dark:text-stone-400">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Order</th>
                        <th class="px-5 py-3.5 text-left">Customer</th>
                        <th class="px-5 py-3.5 text-left">Method & Gateway</th>
                        <th class="px-5 py-3.5 text-left">Transaction ID</th>
                        <th class="px-5 py-3.5 text-left">Amount</th>
                        <th class="px-5 py-3.5 text-left">Status</th>
                        <th class="px-5 py-3.5 text-left">Date</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-stone-700/60 text-stone-700 dark:text-stone-200">
                    @forelse($payments as $order)
                        <tr class="hover:bg-stone-50/60 dark:hover:bg-stone-700/30 transition-colors">
                            {{-- Order ID --}}
                            <td class="px-5 py-4 font-semibold text-stone-900 dark:text-stone-100 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:underline">#{{ $order->id }}</a>
                            </td>

                            {{-- Customer --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-medium text-stone-800 dark:text-stone-100">{{ $order->customer_name }}</p>
                                <p class="text-xs text-stone-400">{{ $order->phone }}</p>
                            </td>

                            {{-- Method & Gateway --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($order->payment_method === 'online')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Card (PayHere)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                        Bank Transfer
                                    </span>
                                @endif
                            </td>

                            {{-- Transaction ID --}}
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-xs">
                                @if($order->transaction_id)
                                    <span class="inline-flex items-center gap-1.5 bg-stone-100 dark:bg-stone-900 px-2 py-1 rounded-md text-stone-700 dark:text-stone-300">
                                        {{ $order->transaction_id }}
                                    </span>
                                @else
                                    <span class="text-stone-400 italic">None</span>
                                @endif
                            </td>

                            {{-- Amount --}}
                            <td class="px-5 py-4 whitespace-nowrap font-bold text-stone-900 dark:text-stone-100">
                                ₨ {{ number_format($order->total_amount, 2) }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Paid
                                    </span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-stone-500 dark:text-stone-400">
                                {{ $order->created_at->format('M d, Y') }}<br>
                                <span class="text-[11px] text-stone-400">{{ $order->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="p-1.5 rounded-lg text-stone-500 hover:text-stone-800 dark:text-stone-400 dark:hover:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-700 transition" title="View Order">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    @if($order->payment_status !== 'paid')
                                        <button type="button" @click="activeOrder = {{ $order->id }}; targetStatus = 'paid'; txId = '{{ $order->transaction_id }}'; modalOpen = true;"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-sm transition">
                                            Mark Paid
                                        </button>
                                    @else
                                        <button type="button" @click="activeOrder = {{ $order->id }}; targetStatus = 'failed'; txId = '{{ $order->transaction_id }}'; modalOpen = true;"
                                                class="px-2 py-1 rounded-lg border border-stone-200 dark:border-stone-600 text-stone-600 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-stone-700 text-[11px] transition">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-stone-500 dark:text-stone-400">
                                No payment transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
            <div class="p-4 border-t border-stone-200 dark:border-stone-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Status Update Modal --}}
    <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="w-full max-w-md bg-white dark:bg-stone-800 rounded-2xl p-6 shadow-xl border border-stone-200 dark:border-stone-700">
            <h3 class="text-lg font-bold text-stone-900 dark:text-stone-100 mb-1">
                Update Payment Status
            </h3>
            <p class="text-xs text-stone-500 dark:text-stone-400 mb-4">
                Updating payment for Order #<span x-text="activeOrder" class="font-bold"></span>
            </p>

            <form :action="'/admin/payments/' + activeOrder + '/status'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">Payment Status</label>
                    <select name="payment_status" x-model="targetStatus" class="w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-900 text-stone-800 dark:text-stone-100 p-2.5 text-sm">
                        <option value="paid">Paid (Confirmed)</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">Transaction ID / Reference (Optional)</label>
                    <input type="text" name="transaction_id" x-model="txId" placeholder="e.g. SLIP-123456 or PayHere ID"
                           class="w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-900 text-stone-800 dark:text-stone-100 p-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">Note (Optional)</label>
                    <textarea name="note" rows="2" placeholder="e.g. Verified bank slip via WhatsApp"
                              class="w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-900 text-stone-800 dark:text-stone-100 p-2.5 text-sm resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100 dark:border-stone-700">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-stone-200 dark:border-stone-600 text-xs font-medium text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-xs font-semibold text-white shadow-sm transition">
                        Save Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
