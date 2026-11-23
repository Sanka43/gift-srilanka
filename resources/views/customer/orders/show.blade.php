@extends('layouts.app')

@section('title', 'Order #' . $order->id)
@section('noindex', '1')

@section('content')
@php
    $steps = ['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
    $stepOrder = array_keys($steps);
    $currentIndex = array_search($order->status, $stepOrder);
    if ($currentIndex === false) {
        $currentIndex = -1;
    }
@endphp
<div class="max-w-4xl mx-auto max-md:px-0 max-md:min-w-0">
    <div class="mb-6 max-md:mb-4">
        <a href="{{ route('customer.orders.index') }}" class="text-sm text-stone-500 hover:text-stone-700 max-md:py-2 inline-block">← Back to My Orders</a>
    </div>

    @if(session('success'))<div class="mb-4 p-3 rounded-lg bg-emerald-100 text-emerald-800 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="mb-4 p-3 rounded-lg bg-blue-100 text-blue-800 text-sm">{{ session('info') }}</div>@endif

    <div class="flex flex-wrap justify-between items-start gap-4 mb-6 max-md:mb-4 max-md:gap-3">
        <h1 class="text-2xl max-md:text-xl font-bold text-stone-800">Order #{{ $order->id }}</h1>
        <x-order-status-badge :status="$order->status" />
    </div>

    {{-- Progress bar: Pending → Processing → Shipped → Delivered --}}
    <div class="bg-white rounded-xl border border-stone-200 p-6 max-md:p-4 mb-6 max-md:mb-4">
        <h2 class="font-semibold text-stone-800 mb-4 max-md:mb-3 max-md:text-base">Order progress</h2>
        <div class="flex items-center justify-between gap-2 max-md:gap-1">
            @foreach($stepOrder as $i => $step)
                <div class="flex flex-1 items-center">
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium
                            @if($currentIndex > $i) bg-emerald-500 text-white
                            @elseif($currentIndex === $i) bg-blue-500 text-white
                            @else bg-stone-200 text-stone-500 @endif">
                            {{ $i + 1 }}
                        </div>
                        <span class="mt-2 max-md:mt-1 text-xs font-medium
                            @if($currentIndex > $i) text-emerald-600
                            @elseif($currentIndex === $i) text-blue-600
                            @else text-stone-400 @endif">{{ $steps[$step] }}</span>
                    </div>
                    @if($i < count($stepOrder) - 1)
                        <div class="flex-1 h-1 mx-1 rounded
                            @if($currentIndex > $i) bg-emerald-500
                            @else bg-stone-200 @endif"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Timeline: status history with date only --}}
    @if($order->statusHistories->isNotEmpty())
        <div class="bg-white rounded-xl border border-stone-200 p-6 max-md:p-4 mb-6 max-md:mb-4">
            <h2 class="font-semibold text-stone-800 mb-4 max-md:mb-3 max-md:text-base">Status timeline</h2>
            <ul class="space-y-3">
                @foreach($order->statusHistories as $history)
                    <li class="flex justify-between items-center text-sm py-2 border-b border-stone-100 last:border-0">
                        <span class="font-medium text-stone-800">{{ ucfirst($history->status) }}</span>
                        <span class="text-stone-500">{{ $history->created_at->format('Y-m-d') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-stone-200 p-6 max-md:p-4 mb-6 max-md:mb-4">
        <h2 class="font-semibold text-stone-800 mb-4 max-md:mb-3 max-md:text-base">Order Info</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-stone-500">Order ID</dt><dd class="font-medium">#{{ $order->id }}</dd></div>
            <div><dt class="text-stone-500">Total</dt><dd class="font-medium">LKR {{ number_format($order->total_amount, 2) }}</dd></div>
            <div><dt class="text-stone-500">Status</dt><dd><x-order-status-badge :status="$order->status" size="sm" /></dd></div>
            @if($order->estimated_delivery_date)
                <div><dt class="text-stone-500">Est. delivery</dt><dd class="font-medium">{{ $order->estimated_delivery_date->format('Y-m-d') }}</dd></div>
            @endif
            @if($order->delivery_date)
                <div><dt class="text-stone-500">Requested delivery</dt><dd class="font-medium">{{ $order->delivery_date->format('Y-m-d') }}</dd></div>
            @endif
            @if($order->delivery_time)
                <div><dt class="text-stone-500">Requested time</dt><dd class="font-medium">{{ substr($order->delivery_time, 0, 5) }}</dd></div>
            @endif
            @if($order->special_note)
                <div class="sm:col-span-2"><dt class="text-stone-500">Special note</dt><dd class="font-medium whitespace-pre-line">{{ $order->special_note }}</dd></div>
            @endif
            <div>
                <dt class="text-stone-500">Payment</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($order->payment_status === 'failed' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                    · {{ $order->payment_method === 'online' ? 'Card (PayHere)' : 'Bank Transfer' }}
                    @if($order->transaction_id)
                        <span class="block text-xs text-stone-500 font-mono mt-0.5">Ref: {{ $order->transaction_id }}</span>
                    @endif
                    @if($order->payment_method === 'online' && $order->payment_status !== 'paid' && $order->status !== \App\Models\Order::STATUS_CANCELLED)
                        <div class="mt-2">
                            <a href="{{ route('checkout.payment.pay', $order) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                Pay Now
                            </a>
                        </div>
                    @endif
                </dd>
            </div>
            <div><dt class="text-stone-500">Date</dt><dd class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
        @if($order->status === 'pending' && !$order->cancel_requested)
            <form action="{{ route('customer.orders.requestCancel', $order->id) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full max-md:min-h-[2.75rem] px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 text-sm">Request cancellation</button>
            </form>
        @elseif($order->cancel_requested)
            <p class="mt-4 text-sm text-amber-700">Cancellation requested. Waiting for admin response.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden max-md:rounded-lg">
        <h3 class="px-6 py-4 max-md:px-4 max-md:py-3 max-md:text-base font-semibold text-stone-800 border-b border-stone-200">Products</h3>
        <div class="overflow-x-auto max-md:-mx-0">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-stone-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-800">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm">LKR {{ number_format($item->price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right">LKR {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="px-6 py-4 max-md:px-4 max-md:py-3 bg-stone-50 border-t border-stone-200 text-right">
            <span class="font-bold text-stone-800">Total: LKR {{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="mt-6 max-md:mt-4">
        <a href="{{ route('customer.orders.index') }}" class="text-primary-600 hover:underline max-md:py-2 inline-block">← Back to My Orders</a>
    </div>
</div>
@endsection
