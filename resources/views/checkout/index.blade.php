@extends('layouts.app')

@section('title', 'Checkout')
@section('noindex', '1')

@section('content')
<style>@media (max-width: 1023px) { body { padding-bottom: 5rem; } }</style>
@php
    $mapOn = $deliverySettings['enabled'];
    $itemCount = $items->sum('quantity');
    $inputBase = 'w-full h-12 px-4 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm';
    $labelBase = 'block text-sm font-semibold text-stone-700 mb-1.5';
    $bankTransfer = config('shop.bank_transfer', [
        'bank' => 'NDB Bank',
        'branch' => 'Rathmalana',
        'account_name' => 'Southern Turn Networks',
        'account_number' => '115511287806',
    ]);
    $slipPhone = config('contact.phone');
    $slipPhoneDigits = preg_replace('/\D+/', '', (string) $slipPhone);
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-28 lg:pb-12">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <nav class="flex items-center gap-1.5 text-xs text-stone-500 mb-2" aria-label="Breadcrumb">
            <a href="{{ route('cart.index') }}" class="hover:text-primary-600 transition-colors">Cart</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-stone-700">Checkout</span>
        </nav>
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-stone-800 leading-tight">Checkout</h1>
                <p class="text-sm text-stone-500 mt-1">Pin where to deliver, tell us who it is for, and pay securely.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
        @csrf
        <input type="hidden" name="checkout_token" value="{{ session('checkout_token') }}">
        @error('cart')
            <div class="lg:col-span-3 rounded-2xl bg-danger-50 border border-danger-200 text-danger-700 px-5 py-4 text-sm font-medium">{{ $message }}</div>
        @enderror

        <div class="lg:col-span-2 space-y-5 sm:space-y-6" x-data="{ paymentMethod: '{{ old('payment_method', 'online') }}' }">

            {{-- 1. Delivery location --}}
            <section class="rounded-3xl bg-white border border-stone-200 shadow-soft overflow-hidden">
                <header class="flex items-center gap-3 px-4 sm:px-7 pt-5 sm:pt-6">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white text-sm font-bold shrink-0">1</span>
                    <div>
                        <h2 class="font-display text-lg font-semibold text-stone-800 leading-tight">Delivery location</h2>
                        <p class="text-xs text-stone-500">{{ $mapOn ? 'Drop a pin on the map. Your delivery charge is calculated from it.' : 'Where should we deliver?' }}</p>
                    </div>
                </header>

                <div class="px-4 sm:px-7 py-5 sm:py-6 space-y-5">
                    @if($mapOn)
                        <div id="delivery-map-block">
                            <div class="flex flex-col sm:flex-row gap-2 mb-3">
                                <div class="relative flex-1">
                                    <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                                    <input type="text" id="dm-search" placeholder="Search area, town or landmark" autocomplete="off" class="{{ $inputBase }} pl-10">
                                </div>
                                <div class="grid grid-cols-2 sm:flex gap-2">
                                    <button type="button" id="dm-search-btn" class="h-12 px-5 rounded-xl border border-stone-300 bg-white text-sm font-semibold text-stone-700 hover:bg-stone-50 active:scale-95 transition">Search</button>
                                    <button type="button" id="dm-locate" class="h-12 px-4 rounded-xl bg-primary-50 border border-primary-200 text-sm font-semibold text-primary-700 hover:bg-primary-100 active:scale-95 transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v1m0 6v1m4-4h1M7 12H8m4 7a7 7 0 100-14 7 7 0 000 14zm0-4a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                        My location
                                    </button>
                                </div>
                            </div>

                            <div id="dm-map" style="height: clamp(18rem, 52vw, 24rem);" class="w-full rounded-2xl border border-stone-200 bg-stone-100 z-0"></div>
                            <p class="lg:hidden text-[11px] text-stone-400 mt-1.5 text-center">Use two fingers to move or zoom the map, tap to drop the pin.</p>
                            <input type="hidden" name="delivery_lat" id="dm-lat" value="{{ old('delivery_lat') }}">
                            <input type="hidden" name="delivery_lng" id="dm-lng" value="{{ old('delivery_lng') }}">

                            <div id="dm-status-box" class="mt-3 flex items-start gap-2.5 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                                <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p id="dm-status" class="text-sm text-stone-600">Tap the map or drag the pin to set where we should deliver. <span class="font-semibold text-stone-800">Pin required.</span></p>
                            </div>
                            @error('delivery_lat')<p class="text-danger-600 text-sm mt-2">{{ $message }}</p>@enderror
                            @error('delivery_lng')<p class="text-danger-600 text-sm mt-2">{{ $message }}</p>@enderror
                        </div>
                    @else
                        <div>
                            <label class="{{ $labelBase }}">District *</label>
                            <input type="text" name="district" value="{{ old('district', 'Colombo') }}" required autocomplete="address-level2" placeholder="Colombo" class="{{ $inputBase }}">
                            @error('district')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    <div>
                        <label class="{{ $labelBase }}">
                            Address / landmark
                            @if($mapOn)<span class="font-normal text-stone-400">(optional)</span>@else * @endif
                        </label>
                        <textarea name="address" rows="2" @unless($mapOn) required @endunless autocomplete="street-address"
                                  placeholder="House number, street, nearby landmark (helps our rider find you)"
                                  class="w-full px-4 py-3 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm resize-none">{{ old('address') }}</textarea>
                        @error('address')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- 2. Contact & schedule --}}
            <section class="rounded-3xl bg-white border border-stone-200 shadow-soft">
                <header class="flex items-center gap-3 px-4 sm:px-7 pt-5 sm:pt-6">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white text-sm font-bold shrink-0">2</span>
                    <div>
                        <h2 class="font-display text-lg font-semibold text-stone-800 leading-tight">Recipient & schedule</h2>
                        <p class="text-xs text-stone-500">Who should our rider contact, and when?</p>
                    </div>
                </header>

                <div class="px-4 sm:px-7 py-5 sm:py-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelBase }}">Full name *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()?->name) }}" required autocomplete="name" autocapitalize="words" placeholder="Your full name" class="{{ $inputBase }}">
                        @error('full_name')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelBase }}">Phone number *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel" placeholder="07X XXX XXXX" class="{{ $inputBase }}">
                        @error('phone')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelBase }}">Delivery date <span class="font-normal text-stone-400">(optional)</span></label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" min="{{ now()->toDateString() }}" class="{{ $inputBase }}">
                        @error('delivery_date')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelBase }}">Delivery time <span class="font-normal text-stone-400">(optional)</span></label>
                        <input type="time" name="delivery_time" value="{{ old('delivery_time') }}" class="{{ $inputBase }}">
                        @error('delivery_time')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelBase }}">Card greeting / special note <span class="font-normal text-stone-400">(optional)</span></label>
                        <textarea name="special_note" rows="3" placeholder="Greeting message, recipient contact, or delivery instructions..."
                                  class="w-full px-4 py-3 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm resize-none">{{ old('special_note') }}</textarea>
                        @error('special_note')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- 3. Payment --}}
            <section class="rounded-3xl bg-white border border-stone-200 shadow-soft">
                <header class="flex items-center gap-3 px-4 sm:px-7 pt-5 sm:pt-6">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white text-sm font-bold shrink-0">3</span>
                    <div>
                        <h2 class="font-display text-lg font-semibold text-stone-800 leading-tight">Payment method</h2>
                        <p class="text-xs text-stone-500">Choose how you would like to pay.</p>
                    </div>
                </header>

                <div class="px-4 sm:px-7 py-5 sm:py-6 space-y-3">
                    <label class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl border-2 border-stone-200 cursor-pointer hover:border-primary-300 transition-all has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                        <div class="flex items-start sm:items-center gap-3">
                            <input type="radio" name="payment_method" value="online" x-model="paymentMethod" {{ old('payment_method', 'online') === 'online' ? 'checked' : '' }} class="size-4 text-primary-600 focus:ring-primary-500 mt-1 sm:mt-0 shrink-0">
                            <div>
                                <span class="font-semibold text-stone-800 text-sm sm:text-base block">Credit / Debit card</span>
                                <span class="text-xs text-stone-500 block mt-0.5">Instant and secure via PayHere</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 pl-7 sm:pl-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-700">VISA</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">Mastercard</span>
                        </div>
                    </label>

                    <label class="relative flex items-start sm:items-center gap-3 p-4 rounded-2xl border-2 border-stone-200 cursor-pointer hover:border-primary-300 transition-all has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                        <input type="radio" name="payment_method" value="cash_on_delivery" x-model="paymentMethod" {{ old('payment_method') === 'cash_on_delivery' ? 'checked' : '' }} class="size-4 text-primary-600 focus:ring-primary-500 mt-1 sm:mt-0 shrink-0">
                        <div>
                            <span class="font-semibold text-stone-800 text-sm sm:text-base block">Bank transfer</span>
                            <span class="text-xs text-stone-500 block mt-0.5">Pay via bank app / CDM and send the slip on WhatsApp</span>
                        </div>
                    </label>
                    @error('payment_method')<p class="text-danger-600 text-sm">{{ $message }}</p>@enderror

                    <div x-show="paymentMethod === 'online'" x-transition class="p-4 rounded-2xl border border-primary-200 bg-primary-50/70 text-xs text-stone-600 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="leading-relaxed">After you place the order you will be redirected to PayHere to finish payment with 3D Secure verification.</p>
                    </div>

                    <div x-show="paymentMethod === 'cash_on_delivery'" x-transition x-cloak x-data="{ copied: false }" class="space-y-3">
                        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4 space-y-2 text-sm">
                            <div class="flex items-center justify-between"><span class="text-stone-500">Bank</span><span class="font-semibold text-stone-900">{{ $bankTransfer['bank'] }}</span></div>
                            <div class="flex items-center justify-between"><span class="text-stone-500">Branch</span><span class="font-semibold text-stone-900">{{ $bankTransfer['branch'] }}</span></div>
                            <div class="flex items-center justify-between gap-3"><span class="text-stone-500">Account name</span><span class="font-semibold text-stone-900 text-right">{{ $bankTransfer['account_name'] }}</span></div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-stone-500">Account number</span>
                                <span class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-stone-900 tracking-wide">{{ $bankTransfer['account_number'] }}</span>
                                    <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $bankTransfer['account_number'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="px-2.5 py-1 rounded-lg bg-stone-200/80 hover:bg-stone-300/80 text-stone-700 text-xs font-medium transition active:scale-95" title="Copy account number">
                                        <span x-show="!copied">Copy</span>
                                        <span x-show="copied" x-cloak class="text-emerald-700 font-bold">Copied!</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                            <p class="font-bold mb-1">Send your transfer slip</p>
                            <p class="text-amber-900/90 leading-relaxed">Share a clear screenshot of the slip to <span class="font-semibold whitespace-nowrap">{{ $slipPhone }}</span> on WhatsApp so we can confirm and dispatch your order.</p>
                            @if(strlen($slipPhoneDigits) >= 9)
                                <a href="https://wa.me/{{ $slipPhoneDigits }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 mt-3 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 active:scale-95 transition">Open WhatsApp</a>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Order summary --}}
        <aside class="lg:col-span-1">
            <div class="rounded-3xl bg-white border border-stone-200 shadow-soft p-5 sm:p-6 lg:sticky lg:top-24">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-lg font-bold text-stone-800">Order summary</h2>
                    <span class="text-xs font-semibold text-primary-700 bg-primary-50 border border-primary-100 rounded-full px-2.5 py-0.5">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</span>
                </div>

                <ul class="divide-y divide-stone-100 max-h-72 overflow-y-auto pr-1">
                    @foreach($items as $item)
                        <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/80 shrink-0 flex items-center justify-center">
                                @if($item['product'] && $item['product']->images->isNotEmpty())
                                    <img src="{{ $item['product']->images->first()->public_url }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-1.5" aria-hidden="true">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-stone-800 line-clamp-2" title="{{ $item['product']->name }}">{{ $item['product']->name }}</p>
                                <p class="text-xs text-stone-500 mt-0.5">Qty {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-sm font-semibold text-stone-800 shrink-0">LKR {{ number_format($item['subtotal'], 0) }}</span>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-5 pt-5 border-t border-stone-200 space-y-2.5 text-sm">
                    <div class="flex justify-between text-stone-600">
                        <dt>Subtotal</dt>
                        <dd class="font-semibold text-stone-800">LKR {{ number_format($subtotal ?? $total, 0) }}</dd>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <dt>Delivery <span class="text-xs text-stone-400">{{ $mapOn ? '(by distance)' : '' }}</span></dt>
                        <dd data-js="delivery" class="font-semibold text-stone-800">LKR {{ number_format($deliveryCharge ?? 0, 0) }}</dd>
                    </div>
                </dl>

                <div class="mt-4 pt-4 border-t border-stone-200 flex justify-between items-baseline">
                    <span class="font-display font-bold text-stone-800 text-lg">Total</span>
                    <span data-js="total" class="font-display font-bold text-primary-700 text-2xl">LKR {{ number_format($total, 0) }}</span>
                </div>

                <button type="submit" class="mt-5 w-full rounded-2xl bg-primary-600 hover:bg-primary-700 active:scale-[0.99] py-4 px-4 text-white font-bold text-base shadow-soft hover:shadow-glow transition-all flex items-center justify-center gap-2 group">
                    <span>Place order</span>
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <ul class="mt-4 space-y-1.5 text-xs text-stone-500">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>Encrypted checkout, details kept private</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>Charge confirmed again when you place the order</li>
                </ul>

                <a href="{{ route('cart.index') }}" class="flex items-center justify-center gap-1.5 mt-4 text-sm font-medium text-stone-500 hover:text-primary-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to cart
                </a>
            </div>
        </aside>

        {{-- Mobile sticky total bar --}}
        <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-stone-200 px-4 pt-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); box-shadow: 0 -4px 16px rgba(0,0,0,.06);">
            <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] text-stone-500 block leading-none">Total</span>
                    <span data-js="total" class="font-display font-bold text-primary-700 text-lg">LKR {{ number_format($total, 0) }}</span>
                </div>
                <button type="submit" class="rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-95 px-6 py-3 text-white font-bold text-sm transition">Place order</button>
            </div>
        </div>
    </form>
</div>

@if($mapOn)
@php
    $mapCfg = [
        'start' => ['lat' => $deliverySettings['start_lat'], 'lng' => $deliverySettings['start_lng'], 'name' => $deliverySettings['start_name']],
        'googleKey' => config('shop.maps.google_browser_key'),
        'quoteUrl' => route('checkout.quote'),
    ];
@endphp
<script src="{{ asset('js/delivery-map.js') }}"></script>
<script>
(function () {
    var cfg = @json($mapCfg);
    var latEl = document.getElementById('dm-lat'), lngEl = document.getElementById('dm-lng');
    var statusEl = document.getElementById('dm-status'), boxEl = document.getElementById('dm-status-box');
    var form = document.getElementById('checkout-form');
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var fmt = function (n) { return n === 0 ? 'Free' : 'LKR ' + Math.round(n).toLocaleString('en-US'); };
    var setAll = function (sel, text) { document.querySelectorAll('[data-js="' + sel + '"]').forEach(function (el) { el.textContent = text; }); };
    var seq = 0;

    function setStatus(html, state) {
        statusEl.innerHTML = html;
        var cls = { ok: 'border-emerald-200 bg-emerald-50', bad: 'border-danger-200 bg-danger-50', idle: 'border-stone-200 bg-stone-50' }[state || 'idle'];
        boxEl.className = 'mt-3 flex items-start gap-2.5 rounded-xl border px-4 py-3 ' + cls;
        statusEl.className = 'text-sm ' + (state === 'bad' ? 'text-danger-700' : 'text-stone-700');
    }

    async function quote(lat, lng) {
        var my = ++seq;
        latEl.value = lat.toFixed(7); lngEl.value = lng.toFixed(7);
        setStatus('Calculating delivery charge...');
        try {
            var res = await fetch(cfg.quoteUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '{{ csrf_token() }}' },
                body: JSON.stringify({ lat: lat, lng: lng })
            });
            var data = await res.json();
            if (my !== seq) return;
            if (!res.ok) {
                latEl.value = ''; lngEl.value = '';
                var msg = data.errors && data.errors.delivery_lat ? data.errors.delivery_lat[0] : (data.message || 'Could not calculate delivery charge.');
                setStatus(msg, 'bad');
                return;
            }
            setAll('delivery', fmt(data.charge));
            setAll('total', 'LKR ' + Math.round(data.total).toLocaleString('en-US'));
            setStatus('Location pinned. <span class="font-semibold">' + data.distance_km.toFixed(1) + ' km</span> away, delivery charge <span class="font-semibold">' + fmt(data.charge) + '</span>.', 'ok');
        } catch (e) {
            if (my === seq) setStatus('Could not calculate delivery charge. Please try again.', 'bad');
        }
    }

    var initial = latEl.value && lngEl.value ? { lat: parseFloat(latEl.value), lng: parseFloat(lngEl.value) } : null;
    DeliveryMap.init({
        el: document.getElementById('dm-map'),
        provider: cfg.googleKey ? 'google' : 'osm',
        googleKey: cfg.googleKey,
        center: initial || cfg.start,
        zoom: 12,
        initial: initial,
        startPoint: cfg.start,
        searchInput: document.getElementById('dm-search'),
        searchButton: document.getElementById('dm-search-btn'),
        locateButton: document.getElementById('dm-locate'),
        onPick: quote
    }).then(function () { if (initial) quote(initial.lat, initial.lng); })
      .catch(function () { setStatus('Map failed to load. Please refresh the page.', 'bad'); });

    form.addEventListener('submit', function (e) {
        if (!latEl.value || !lngEl.value) {
            e.preventDefault();
            setStatus('Please pin your delivery location on the map first.', 'bad');
            document.getElementById('delivery-map-block').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
@endif
@endsection
