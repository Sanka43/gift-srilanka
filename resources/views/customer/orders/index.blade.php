@extends('layouts.app')

@section('title', 'My Orders')
@section('noindex', '1')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('shop.index') }}" class="text-sm text-stone-500 hover:text-stone-700">← Back to Shop</a>
    </div>
    <h1 class="text-2xl font-bold text-stone-800 mb-6">My Orders</h1>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-stone-800">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-stone-800">LKR {{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <x-order-status-badge :status="$order->status ?? 'pending'" size="sm" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">You have no orders yet. <a href="{{ route('shop.index') }}" class="text-primary-600 hover:underline font-medium">Start shopping</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-stone-200 bg-stone-50/50">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
