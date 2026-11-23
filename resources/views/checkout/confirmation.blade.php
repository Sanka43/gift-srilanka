@extends('layouts.app')

@section('title', 'Order Confirmation')
@section('noindex', '1')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
    <div class="rounded-2xl sm:rounded-3xl bg-primary-50 border-2 border-primary-200 p-6 sm:p-8 text-center shadow-soft">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-primary-500 flex items-center justify-center mx-auto mb-3 sm:mb-4 shadow-sm">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-primary-800 mb-1.5 font-display">Thank you for your order!</h1>
        <p class="text-primary-700 font-medium text-sm sm:text-base">Order #{{ $order->id }} has been placed successfully.</p>
    </div>

    <div class="rounded-2xl sm:rounded-3xl bg-white border border-stone-200 shadow-soft p-4 sm:p-6 mt-6">
        <h2 class="font-display text-base sm:text-lg font-semibold text-stone-800 mb-3 pb-2 border-b border-stone-100">Order Details</h2>
        
        <div class="divide-y divide-stone-100 text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between gap-3">
                <span class="text-stone-500">Order ID</span>
                <span class="font-semibold text-stone-800">#{{ $order->id }}</span>
            </div>
            <div class="py-2.5 flex items-center justify-between gap-3">
                <span class="text-stone-500">Status</span>
                <div><x-order-status-badge :status="$order->status" size="sm" /></div>
            </div>
            @if($order->delivery_charge > 0)
                <div class="py-2.5 flex items-center justify-between gap-3">
                    <span class="text-stone-500">Subtotal</span>
                    <span class="font-semibold text-stone-800">LKR {{ number_format($order->total_amount - $order->delivery_charge, 2) }}</span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-3">
                    <span class="text-stone-500">Delivery charge</span>
                    <span class="font-semibold text-stone-800">LKR {{ number_format($order->delivery_charge, 2) }}</span>
                </div>
            @endif
            <div class="py-2.5 flex items-center justify-between gap-3">
                <span class="text-stone-500 font-medium">Total</span>
                <span class="font-bold text-primary-700 text-sm sm:text-base">LKR {{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="py-2.5 flex items-center justify-between gap-3">
                <span class="text-stone-500">Payment</span>
                <div class="font-semibold text-stone-800 text-right">
                    @if($order->payment_method === 'online')
                        @if($order->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Paid via Card (PayHere)
                            </span>
                            @if($order->transaction_id)
                                <span class="block text-[11px] text-stone-500 font-mono mt-0.5">Ref: {{ $order->transaction_id }}</span>
                            @endif
                        @elseif($order->payment_status === 'failed')
                            <span class="inline-flex items-center gap-1 text-danger-700 bg-danger-50 px-2.5 py-1 rounded-lg border border-danger-200 text-xs font-semibold">
                                Payment Failed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 text-xs font-semibold">
                                Processing
                            </span>
                        @endif
                    @else
                        Bank Transfer
                    @endif
                </div>
            </div>
        </div>

        @if($order->payment_method === 'online' && $order->payment_status !== 'paid' && $order->status !== \App\Models\Order::STATUS_CANCELLED)
            <div class="mt-4 p-3.5 sm:p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="font-semibold text-amber-900">Payment not completed?</p>
                    <p class="text-xs text-amber-800">If your payment window was closed, you can retry checkout now.</p>
                </div>
                <a href="{{ route('checkout.payment.pay', $order) }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-sm text-center transition">
                    Complete Payment
                </a>
            </div>
        @endif

        <h3 class="font-display font-semibold text-stone-800 mt-6 mb-3 pt-3 border-t border-stone-100 text-sm sm:text-base">Ordered Items</h3>
        <ul class="divide-y divide-stone-100">
            @foreach($order->items as $item)
                <li class="py-2.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                    <span class="text-stone-700 min-w-0 truncate">{{ $item->product->name }} <span class="text-stone-400 font-normal">× {{ $item->quantity }}</span></span>
                    <span class="font-semibold text-stone-800 shrink-0">LKR {{ number_format($item->subtotal, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <p class="mt-6 text-center text-stone-500 text-xs sm:text-sm leading-relaxed px-2">
        @if($order->payment_method === 'online')
            A confirmation receipt and order details have been sent to your email.
        @else
            Please transfer the amount using the bank details provided and send the slip to our WhatsApp.
        @endif
    </p>

    <div class="mt-6 flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
        <a href="{{ route('shop.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 shadow-soft transition">Continue Shopping</a>
        @auth
            @if($order->user_id && (int) $order->user_id === (int) auth()->id())
                <a href="{{ route('customer.orders.show', $order) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-2xl border-2 border-stone-200 text-stone-700 font-semibold text-sm hover:bg-stone-50 transition">View order details</a>
            @endif
        @endauth
    </div>
</div>
@endsection
