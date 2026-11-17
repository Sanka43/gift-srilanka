@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="max-w-4xl">
    <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
        <h2 class="text-xl font-bold text-stone-800 dark:text-stone-100">Order #{{ $order->id }}</h2>
        <x-order-status-badge :status="$order->status" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="admin-card bg-white dark:bg-stone-800/90 rounded-xl border border-stone-200 dark:border-stone-700 p-6">
            <h3 class="font-semibold text-stone-800 dark:text-stone-100 mb-3">Customer & Delivery</h3>
            <dl class="space-y-2 text-sm text-stone-700 dark:text-stone-200">
                <div><dt class="text-stone-500 dark:text-stone-400">Name</dt><dd class="font-medium text-stone-800 dark:text-stone-100">{{ $order->full_name }}</dd></div>
                <div><dt class="text-stone-500 dark:text-stone-400">Phone</dt><dd>{{ $order->phone }}</dd></div>
                <div><dt class="text-stone-500 dark:text-stone-400">District</dt><dd>{{ $order->district }}</dd></div>
                <div><dt class="text-stone-500 dark:text-stone-400">Address</dt><dd>{{ $order->address }}</dd></div>
                @if($order->delivery_date)
                    <div><dt class="text-stone-500 dark:text-stone-400">Requested delivery</dt><dd>{{ $order->delivery_date->format('Y-m-d') }}</dd></div>
                @endif
                @if($order->delivery_time)
                    <div><dt class="text-stone-500 dark:text-stone-400">Requested time</dt><dd>{{ substr($order->delivery_time, 0, 5) }}</dd></div>
                @endif
                @if($order->special_note)
                    <div><dt class="text-stone-500 dark:text-stone-400">Special note</dt><dd class="whitespace-pre-line">{{ $order->special_note }}</dd></div>
                @endif
                @if($order->user)
                    <div><dt class="text-stone-500 dark:text-stone-400">User</dt><dd>{{ $order->user->email }}</dd></div>
                @endif
            </dl>
        </div>
        <div class="admin-card bg-white dark:bg-stone-800/90 rounded-xl border border-stone-200 dark:border-stone-700 p-6">
            <h3 class="font-semibold text-stone-800 dark:text-stone-100 mb-3">Payment</h3>
            <dl class="space-y-2 text-sm text-stone-700 dark:text-stone-200">
                <div><dt class="text-stone-500 dark:text-stone-400">Method</dt><dd>{{ $order->payment_method === 'online' ? 'Online' : 'Cash on Delivery' }}</dd></div>
                <div><dt class="text-stone-500 dark:text-stone-400">Payment status</dt><dd><span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">{{ ucfirst($order->payment_status) }}</span></dd></div>
                <div><dt class="text-stone-500 dark:text-stone-400">Total</dt><dd class="font-bold text-lg text-stone-900 dark:text-stone-100">LKR {{ number_format($order->total_amount, 2) }}</dd></div>
                @if($order->estimated_delivery_date)
                    <div><dt class="text-stone-500 dark:text-stone-400">Est. delivery</dt><dd>{{ $order->estimated_delivery_date->format('Y-m-d') }}</dd></div>
                @endif
            </dl>
            <a href="{{ route('admin.orders.edit', $order) }}" class="mt-4 inline-block px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Update status</a>
        </div>
    </div>

    @if($order->cancel_requested)
        <div class="admin-card bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/70 rounded-xl p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="font-medium text-amber-800 dark:text-amber-300">Customer requested cancellation for this order.</p>
            <form action="{{ route('admin.orders.cancelApprove', $order) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-danger-600 text-white text-sm rounded-lg hover:bg-danger-700">Approve cancellation</button>
            </form>
            <form action="{{ route('admin.orders.cancelReject', $order) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 border border-stone-300 dark:border-stone-600 rounded-lg text-stone-700 dark:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-700/50 text-sm">Reject request</button>
            </form>
        </div>
    @endif

    <div class="admin-card bg-white dark:bg-stone-800/90 rounded-xl border border-stone-200 dark:border-stone-700 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-stone-800 dark:text-stone-100 border-b border-stone-200 dark:border-stone-700">Items</h3>
        <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
            <thead class="bg-stone-50 dark:bg-stone-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-stone-500 dark:text-stone-400 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-800 dark:text-stone-100">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-700 dark:text-stone-200">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-stone-700 dark:text-stone-200">LKR {{ number_format($item->price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-stone-700 dark:text-stone-200">LKR {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex flex-wrap gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-primary-600 dark:text-primary-400 hover:underline">← Back to Orders</a>
        <a href="{{ route('admin.orders.edit', $order) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Edit order</a>
    </div>
</div>
@endsection
