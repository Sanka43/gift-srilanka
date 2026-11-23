@extends('layouts.app')

@section('title', 'Order Confirmation')
@section('noindex', '1')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="rounded-2xl bg-primary-50 border-2 border-primary-200 p-8 text-center shadow-soft">
        <div class="w-16 h-16 rounded-full bg-primary-500 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-primary-800 mb-2">Thank you for your order!</h1>
        <p class="text-primary-700 font-medium">Order #{{ $order->id }} has been placed successfully.</p>
    </div>

    <div class="rounded-2xl bg-white border border-stone-200 shadow-soft p-6 mt-6">
        <h2 class="font-display font-semibold text-stone-800 mb-4">Order Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
            <dt class="text-stone-500">Order ID</dt>
            <dd class="font-semibold text-stone-800">{{ $order->id }}</dd>
            <dt class="text-stone-500">Status</dt>
            <dd><x-order-status-badge :status="$order->status" size="sm" /></dd>
            @if($order->delivery_charge > 0)
            <dt class="text-stone-500">Subtotal</dt>
            <dd class="font-semibold text-stone-800">LKR {{ number_format($order->total_amount - $order->delivery_charge, 2) }}</dd>
            <dt class="text-stone-500">Delivery charge</dt>
            <dd class="font-semibold text-stone-800">LKR {{ number_format($order->delivery_charge, 2) }}</dd>
            @endif
            <dt class="text-stone-500">Total</dt>
            <dd class="font-semibold text-stone-800">LKR {{ number_format($order->total_amount, 2) }}</dd>
            <dt class="text-stone-500">Payment</dt>
            <dd class="font-semibold text-stone-800">
                @if($order->payment_method === 'online')
                    @if($order->payment_status === 'paid')
                        <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Paid via Card (PayHere)
                        </span>
                        @if($order->transaction_id)
                            <span class="block text-[11px] text-stone-500 font-mono mt-0.5">Ref: {{ $order->transaction_id }}</span>
                        @endif
                    @elseif($order->payment_status === 'failed')
                        <span class="inline-flex items-center gap-1 text-danger-700 bg-danger-50 px-2 py-0.5 rounded-lg border border-danger-200 text-xs font-semibold">
                            Payment Failed
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200 text-xs font-semibold">
                            Processing
                        </span>
                    @endif
                @else
                    Bank Transfer
                @endif
            </dd>
        </dl>

        @if($order->payment_method === 'online' && $order->payment_status !== 'paid' && $order->status !== \App\Models\Order::STATUS_CANCELLED)
            <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-amber-900">Payment not completed?</p>
                    <p class="text-xs text-amber-800">If your payment window was closed, you can retry checkout now.</p>
                </div>
                <a href="{{ route('checkout.payment.pay', $order) }}" class="shrink-0 px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-sm transition">
                    Complete Payment
                </a>
            </div>
        @endif

        <h3 class="font-display font-semibold text-stone-800 mt-6 mb-2">Items</h3>
        <ul class="divide-y divide-stone-200">
            @foreach($order->items as $item)
                <li class="py-3 flex justify-between text-sm">
                    <span class="text-stone-700">{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <span class="font-semibold text-stone-800">LKR {{ number_format($item->subtotal, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <p class="mt-6 text-center text-stone-500 text-sm">
        @if($order->payment_method === 'online')
            A confirmation receipt and order details have been sent to your email.
        @else
            Please transfer the amount using the bank details provided and send the slip to our WhatsApp.
        @endif
    </p>
    <div class="mt-6 flex flex-wrap justify-center gap-4">
        <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-primary-500 text-white font-semibold hover:bg-primary-600 shadow-soft transition">Continue Shopping</a>
        @auth
            @if($order->user_id && (int) $order->user_id === (int) auth()->id())
                <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-2xl border-2 border-stone-200 text-stone-700 font-semibold hover:bg-stone-50 transition">View order details</a>
            @endif
        @endauth
    </div>
</div>
@endsection
