@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between max-md:gap-3">
        <div>
            <h1 class="font-display text-2xl max-md:text-xl font-bold text-stone-800 dark:text-stone-100">Orders</h1>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Manage and update order status.</p>
        </div>
        <a href="{{ route('admin.orders.export') }}" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 px-4 py-2.5 text-sm font-medium text-stone-700 dark:text-stone-200 shadow-sm transition hover:bg-stone-50 dark:hover:bg-stone-700/60">
            <svg class="h-5 w-5 text-stone-400 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>

    {{-- Search + Filter --}}
    <form action="{{ route('admin.orders.index') }}" method="GET" class="admin-card flex flex-wrap items-center gap-3 rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 p-4 max-md:p-3 max-md:rounded-xl">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="sr-only">Search</label>
            <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Order ID, customer, phone..."
                   class="admin-input w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-900 text-stone-800 dark:text-stone-100 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
        </div>
        <div class="w-full sm:w-40">
            <label for="status" class="sr-only">Status</label>
            <select id="status" name="status" class="admin-input w-full rounded-xl border border-stone-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-200 py-2.5 px-4 text-sm focus:border-primary-400 focus:outline-none">
                <option value="">All statuses</option>
                @foreach(\App\Models\Order::STATUSES as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-primary-500 px-4 py-2.5 max-md:min-h-[2.75rem] max-md:w-full text-sm font-semibold text-white shadow-sm hover:bg-primary-600 transition">Apply</button>
    </form>

    {{-- Table --}}
    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 dark:border-stone-700 bg-white dark:bg-stone-800/90 max-md:rounded-xl">
        <div class="overflow-x-auto -mx-0">
            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                <thead class="bg-stone-50/80 dark:bg-stone-800">
                    <tr>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">ID</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Customer</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Total</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Status</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Payment</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Date</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 dark:divide-stone-700 bg-white dark:bg-stone-800/80">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/50 dark:hover:bg-stone-700/40 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-stone-800 dark:text-stone-100">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm text-stone-600 dark:text-stone-200">{{ $order->full_name }}<br><span class="text-stone-500 dark:text-stone-400">{{ $order->phone }}</span></td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-stone-800 dark:text-stone-100">LKR {{ number_format($order->total_amount, 0) }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="post" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="rounded-lg border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 text-stone-800 dark:text-stone-200 text-sm focus:border-primary-500 focus:ring-primary-500 dark:focus:ring-primary-500/30">
                                        @foreach(\App\Models\Order::STATUSES as $status)
                                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $order->payment_status === 'paid' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300' : 'bg-warning-100 text-warning-700 dark:bg-warning-900/40 dark:text-warning-300' }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500 dark:text-stone-400">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-stone-500 dark:text-stone-400">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 dark:border-stone-700 px-6 py-3 max-md:px-4 max-md:py-2 bg-stone-50/30 dark:bg-stone-800">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
