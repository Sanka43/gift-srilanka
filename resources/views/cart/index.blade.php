@extends('layouts.app')

@section('title', 'Shopping Cart')
@section('noindex', '1')
@section('fullBleed') @endsection

@section('content')
<div class="cart-page min-h-screen bg-primary-50/40">
    {{-- Small header strip --}}
    <!-- <div class="border-b border-stone-200/80 bg-white/70 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 max-md:px-5 max-md:py-3.5">
            <h1 class="font-display text-2xl sm:text-3xl max-md:text-xl font-bold text-stone-800">Shopping Cart</h1>
            <nav class="mt-1.5 flex items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors duration-300">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('shop.index') }}" class="hover:text-primary-600 transition-colors duration-300">Shop</a>
                <span aria-hidden="true">/</span>
                <span class="text-stone-700 font-medium" aria-current="page">Cart</span>
            </nav>
        </div>
    </div> -->  

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 max-md:px-5 max-md:py-6">
        @if($items->isEmpty())
            {{-- Empty cart state --}}
            <div class="empty-cart flex flex-col items-center justify-center py-16 sm:py-24 px-6 max-md:py-12 max-md:px-5">
                <div class="empty-cart-illustration w-40 h-40 sm:w-48 sm:h-48 flex items-center justify-center rounded-full bg-white shadow-soft border border-stone-100 mb-8 transition-transform duration-300">
                    <svg class="w-20 h-20 sm:w-24 sm:h-24 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl sm:text-2xl font-semibold text-stone-800 mb-2">Your cart is empty</h2>
                <p class="text-stone-500 text-center max-w-sm mb-8">Add something special from our collection to get started.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-primary-500 text-white font-semibold hover:bg-primary-600 shadow-soft transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 max-md:gap-6">
                {{-- Left: Cart items section --}}
                <div class="lg:col-span-2 space-y-1">
                    <div class="overflow-hidden">
                        @foreach($items as $item)
                            @php
                                $product = $item['product'];
                                $outOfStock = $product->stock < 1;
                                $safeQty = $outOfStock ? 0 : min((int) $item['quantity'], (int) $product->stock);
                                if ($safeQty < 1) { $safeQty = 1; }
                                $description = $product->description ? \Str::limit(strip_tags($product->description), 80) : $product->name . ' – LKR ' . number_format($item['price'], 0) . ' each';
                            @endphp

                            {{-- Mobile: card layout (circular image, name + item total, LKR each, qty + delete) --}}
                            <div class="md:hidden cart-item-row group rounded-2xl bg-white shadow-soft border border-stone-100 p-4 {{ !$loop->first ? 'mt-4' : '' }}">
                                <div class="flex gap-4">
                                    <a href="{{ route('products.show', $product->slug) }}" class="flex-shrink-0 w-20 h-20 rounded-full overflow-hidden bg-stone-100 border-2 border-stone-200 flex items-center justify-center">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ $product->images->first()->public_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-3" aria-hidden="true">
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <a href="{{ route('products.show', $product->slug) }}" class="font-display font-semibold text-stone-800 hover:text-primary-600 transition-colors line-clamp-1">{{ $product->name }}</a>
                                            <span class="font-display font-bold text-stone-800 flex-shrink-0">LKR {{ number_format($item['subtotal'], 0) }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-stone-500">LKR {{ number_format($item['price'], 0) }} each</p>
                                        @if($outOfStock)
                                            <p class="mt-2 text-xs text-danger-600 font-medium">Out of stock</p>
                                        @else
                                            <div class="mt-3 flex items-center justify-between gap-2">
                                                <form action="{{ route('cart.update', $product) }}" method="POST" class="flex items-center rounded-full bg-stone-900 overflow-hidden">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $safeQty }}" class="cart-qty-input">
                                                    <button type="button" class="cart-qty-minus flex items-center justify-center w-9 h-9 text-white hover:bg-stone-700 transition-colors flex-shrink-0" aria-label="Decrease quantity">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                                    </button>
                                                    <span class="cart-qty-display min-w-[1.75rem] text-center text-sm font-semibold text-white">{{ $safeQty }}</span>
                                                    <button type="button" class="cart-qty-plus flex items-center justify-center w-9 h-9 text-white hover:bg-stone-700 transition-colors flex-shrink-0" aria-label="Increase quantity" data-max="{{ $product->stock }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('cart.remove', $product) }}" method="POST" onsubmit="return confirm('Remove this item from your cart?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center justify-center w-10 h-10 rounded-full text-danger-500 hover:text-danger-600 hover:bg-danger-50 transition-all" aria-label="Remove item">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Desktop: original row layout --}}
                            <div class="hidden md:flex cart-item-row group flex-col sm:flex-row gap-4 sm:gap-5 py-5 sm:py-6 {{ !$loop->first ? 'border-t border-stone-100' : '' }} transition-colors duration-300 hover:bg-stone-50/60 rounded-xl -mx-1 px-1">
                                <div class="flex flex-col sm:flex-row gap-4 flex-1 min-w-0">
                                    <a href="{{ route('products.show', $product->slug) }}" class="flex-shrink-0 w-full sm:w-28 h-28 sm:h-28 rounded-2xl overflow-hidden bg-stone-100 ring-1 ring-stone-100">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ $product->images->first()->public_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-3" aria-hidden="true">
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <a href="{{ route('products.show', $product->slug) }}" class="font-display font-semibold text-stone-800 hover:text-primary-600 transition-colors duration-300">{{ $product->name }}</a>
                                                @if($product->category)
                                                    <span class="inline-block mt-1 text-xs font-medium text-stone-500 bg-stone-100 text-stone-600 px-2.5 py-0.5 rounded-lg">{{ $product->category->name }}</span>
                                                @endif
                                            </div>
                                            @if($outOfStock)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-danger-100 text-danger-700 border border-danger-200">Out of Stock</span>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-sm text-stone-600">LKR {{ number_format($item['price'], 0) }} each</p>
                                        @if($outOfStock)
                                            <p class="mt-2 text-sm text-danger-600 font-medium">Currently unavailable</p>
                                        @else
                                            <div class="mt-4 flex flex-wrap items-center gap-4">
                                                <form action="{{ route('cart.update', $product) }}" method="POST" class="flex items-center gap-0 rounded-xl border-2 border-stone-200 overflow-hidden bg-white">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $safeQty }}" class="cart-qty-input">
                                                    <button type="button" class="cart-qty-minus flex items-center justify-center w-10 h-10 text-stone-500 hover:bg-stone-100 hover:text-stone-700 transition-colors duration-300" aria-label="Decrease quantity">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                    </button>
                                                    <span class="cart-qty-display inline-flex items-center justify-center min-w-[2.5rem] h-10 text-sm font-semibold text-stone-800">{{ $safeQty }}</span>
                                                    <button type="button" class="cart-qty-plus flex items-center justify-center w-10 h-10 text-stone-500 hover:bg-stone-100 hover:text-stone-700 transition-colors duration-300" aria-label="Increase quantity" data-max="{{ $product->stock }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('cart.remove', $product) }}" method="POST" onsubmit="return confirm('Remove this item from your cart?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center justify-center w-10 h-10 rounded-xl text-stone-400 hover:text-danger-600 hover:bg-danger-50 transition-all duration-300" aria-label="Remove item">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="sm:text-right flex-shrink-0">
                                    <p class="text-sm text-stone-500 sm:block">Subtotal</p>
                                    <p class="font-display font-bold text-stone-800 text-lg">LKR {{ number_format($item['subtotal'], 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Order summary (sticky) --}}
                <div class="lg:col-span-1">
                    <div class="cart-summary-card rounded-[20px] bg-white shadow-soft border border-stone-100 p-6 max-md:p-5 sticky top-24 transition-shadow duration-300">
                        <h2 class="font-display font-semibold text-stone-800 text-lg mb-5">Order Summary</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-stone-700">
                                <span>Subtotal</span>
                                <span class="font-medium">LKR {{ number_format($subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between text-stone-700">
                                <span>Delivery</span>
                                <span class="font-medium">LKR {{ number_format($deliveryCharge, 0) }}</span>
                            </div>
                            @if($discount > 0)
                                <div class="flex justify-between text-primary-600">
                                    <span>Discount</span>
                                    <span class="font-medium">- LKR {{ number_format($discount, 0) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="my-5 border-t border-stone-200"></div>
                        <div class="flex justify-between items-baseline mb-6">
                            <span class="font-display font-semibold text-stone-800">Total</span>
                            <span class="font-display font-bold text-xl text-stone-900">LKR {{ number_format($total, 0) }}</span>
                        </div>
                        <div class="promo-section mb-5">
                            <label for="promo-code" class="sr-only">Promo code</label>
                            <div class="flex gap-2">
                                <input type="text" id="promo-code" name="promo_code" placeholder="Promo code" class="flex-1 rounded-xl border-2 border-stone-200 px-4 py-3 text-sm placeholder-stone-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all duration-300">
                                <button type="button" class="cart-promo-apply px-4 py-3 rounded-xl border-2 border-stone-200 text-sm font-semibold text-stone-700 hover:bg-stone-50 hover:border-stone-300 transition-all duration-300">Apply</button>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="cart-checkout-btn flex items-center justify-center w-full h-[52px] rounded-2xl bg-primary-500 text-white font-semibold shadow-soft hover:bg-primary-600 transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('shop.index') }}" class="block mt-4 text-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors duration-300">Continue Shopping</a>
                    </div>
                </div>
            </div>

            {{-- Mobile: sticky bottom checkout bar --}}
            <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 p-4 max-md:p-3.5 bg-white/95 backdrop-blur-sm border-t border-stone-200 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.08)]">
                <div class="max-w-6xl mx-auto flex items-center justify-between gap-4 max-md:gap-3">
                    <div>
                        <p class="text-xs text-stone-500">Total</p>
                        <p class="font-display font-bold text-lg max-md:text-base text-stone-900">LKR {{ number_format($total, 0) }}</p>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="flex-1 flex items-center justify-center h-12 max-md:min-h-[2.75rem] rounded-xl bg-primary-500 text-white font-semibold hover:bg-primary-600 transition-all duration-300 active:scale-[0.98]">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
            <div class="lg:hidden h-24" aria-hidden="true"></div>
        @endif
    </div>
</div>

@if(!$items->isEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    var duration = 300;
    document.querySelectorAll('.cart-item-row').forEach(function(row) {
        var form = row.querySelector('form[action*="cart/update"]');
        if (!form) return;
        var input = form.querySelector('.cart-qty-input');
        var display = form.querySelector('.cart-qty-display');
        var minus = form.querySelector('.cart-qty-minus');
        var plus = form.querySelector('.cart-qty-plus');
        var max = plus ? parseInt(plus.getAttribute('data-max') || '99', 10) : 99;
        function updateUi() {
            var q = parseInt(input.value, 10) || 1;
            q = Math.max(1, Math.min(max, q));
            input.value = q;
            display.textContent = q;
        }
        minus && minus.addEventListener('click', function() {
            var q = parseInt(input.value, 10) || 1;
            input.value = Math.max(1, q - 1);
            updateUi();
            form.requestSubmit();
        });
        plus && plus.addEventListener('click', function() {
            var q = parseInt(input.value, 10) || 1;
            input.value = Math.min(max, q + 1);
            updateUi();
            form.requestSubmit();
        });
    });
    document.querySelectorAll('.cart-promo-apply').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var field = document.getElementById('promo-code');
            if (field && field.value.trim()) {
                btn.textContent = 'Applied';
                btn.disabled = true;
                btn.classList.add('opacity-75');
            }
        });
    });
});
</script>
@endif
@endsection
