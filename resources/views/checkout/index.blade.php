@extends('layouts.app')

@section('title', 'Checkout')
@section('noindex', '1')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 md:py-10">
    <section class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-emerald-50/70 p-4 sm:p-6 md:p-8 mb-6 sm:mb-8 md:mb-10 shadow-soft">
        <div class="absolute -top-24 -right-16 h-56 w-56 rounded-full bg-primary-200/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-10 h-48 w-48 rounded-full bg-emerald-200/40 blur-3xl pointer-events-none"></div>
        <div class="relative grid grid-cols-1 lg:grid-cols-5 gap-5 lg:gap-6 items-center">
            <div class="lg:col-span-2 text-center sm:text-left">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-primary-700 mb-2 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Secure Checkout
                </span>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-stone-800 mb-1.5 leading-tight">Complete Your Order</h1>
                <p class="text-xs sm:text-sm text-stone-600">Enter delivery details, confirm payment, and place your order safely.</p>
            </div>

            <div class="lg:col-span-3 rounded-2xl border border-primary-200/70 bg-white/90 backdrop-blur-sm p-3.5 sm:p-4 shadow-2xs">
                <div class="flex items-center justify-between max-w-sm sm:max-w-md mx-auto">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center text-center flex-1">
                        <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-bold shadow-sm ring-4 ring-primary-100">1</span>
                        <p class="mt-1.5 text-[11px] sm:text-xs font-semibold text-stone-800">Delivery</p>
                    </div>
                    <!-- Connector line -->
                    <div class="h-0.5 flex-1 bg-primary-300 mx-1.5 sm:mx-2 -mt-4"></div>
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center text-center flex-1">
                        <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-bold shadow-sm ring-4 ring-primary-100">2</span>
                        <p class="mt-1.5 text-[11px] sm:text-xs font-semibold text-stone-800">Payment</p>
                    </div>
                    <!-- Connector line -->
                    <div class="h-0.5 flex-1 bg-stone-200 mx-1.5 sm:mx-2 -mt-4"></div>
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center text-center flex-1">
                        <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-stone-100 text-stone-500 text-xs font-bold border border-stone-200">3</span>
                        <p class="mt-1.5 text-[11px] sm:text-xs font-semibold text-stone-500">Review</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
        @csrf
        <input type="hidden" name="checkout_token" value="{{ session('checkout_token') }}">
        @error('cart')
            <div class="lg:col-span-3 rounded-2xl bg-danger-50 border border-danger-200 text-danger-700 px-4 sm:px-5 py-3.5 sm:py-4 text-sm font-medium">{{ $message }}</div>
        @enderror

        {{-- Mobile Collapsible Order Summary Bar (Visible only on mobile/tablet < lg) --}}
        <div class="lg:hidden col-span-1" x-data="{ openSummary: false }">
            <button type="button" 
                    @click="openSummary = !openSummary"
                    class="w-full flex items-center justify-between p-4 rounded-2xl bg-white border border-stone-200 shadow-soft text-left transition hover:bg-stone-50/80 active:scale-[0.99]">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-stone-800">
                            <span x-text="openSummary ? 'Hide order summary' : 'Show order summary'"></span>
                            <svg class="w-4 h-4 text-stone-500 transition-transform duration-200" :class="{ 'rotate-180': openSummary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <span class="text-[11px] text-stone-500 truncate block">{{ $items->sum('quantity') }} {{ Str::plural('item', $items->sum('quantity')) }} in cart</span>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-2">
                    <span class="text-[11px] text-stone-400 block font-normal">Total</span>
                    <span class="text-base font-bold text-primary-700 font-display">LKR {{ number_format($total, 0) }}</span>
                </div>
            </button>

            <div x-show="openSummary" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 x-cloak 
                 class="mt-2.5 p-4 rounded-2xl bg-white border border-stone-200 shadow-soft space-y-3.5">
                <ul class="divide-y divide-stone-100 max-h-64 overflow-y-auto pr-1">
                    @foreach($items as $item)
                        <li class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/80 shrink-0 flex items-center justify-center">
                                @if($item['product'] && $item['product']->images->isNotEmpty())
                                    <img src="{{ $item['product']->images->first()->public_url }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-1.5" aria-hidden="true">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-stone-800 line-clamp-1">{{ $item['product']->name }}</p>
                                <p class="text-[11px] text-stone-500 mt-0.5">Qty: {{ $item['quantity'] }} × LKR {{ number_format($item['price'], 0) }}</p>
                            </div>
                            <span class="text-xs font-bold text-stone-800 shrink-0">LKR {{ number_format($item['subtotal'], 0) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="pt-3 border-t border-stone-100 space-y-2 text-xs">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($subtotal ?? $total, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Delivery Charge</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($deliveryCharge ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-800 font-bold text-sm pt-2 border-t border-stone-100">
                        <span>Total Due</span>
                        <span class="text-primary-700 font-display text-base">LKR {{ number_format($total, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6" x-data="{ paymentMethod: '{{ old('payment_method', 'online') }}' }">
            {{-- Step 1: Contact & Delivery --}}
            <div class="rounded-2xl sm:rounded-3xl bg-white border border-stone-200 shadow-soft p-4 sm:p-6 md:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 sm:mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-primary-700 text-sm font-bold">1</span>
                        <div>
                            <h2 class="font-display text-base sm:text-lg font-semibold text-stone-800 leading-tight">Contact & Delivery</h2>
                            <p class="text-xs text-stone-500">Where and when we should deliver your order</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-stone-500 rounded-full border border-stone-200 bg-stone-50 px-2.5 py-1">* Required</span>
                </div>

                <div class="rounded-2xl border border-stone-100 bg-stone-50/70 p-3 sm:p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Full Name *</label>
                            <input type="text" 
                                   name="full_name" 
                                   value="{{ old('full_name', auth()->user()?->name) }}" 
                                   required 
                                   autocomplete="name" 
                                   autocapitalize="words"
                                   placeholder="Your full name"
                                   class="w-full h-11 sm:h-12 px-3.5 sm:px-4 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs">
                            @error('full_name')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Phone Number *</label>
                            <input type="tel" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   autocomplete="tel" 
                                   inputmode="tel"
                                   placeholder="07X XXX XXXX"
                                   class="w-full h-11 sm:h-12 px-3.5 sm:px-4 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs">
                            @error('phone')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">District *</label>
                            <input type="text" 
                                   name="district" 
                                   value="{{ old('district', 'Colombo') }}" 
                                   required 
                                   autocomplete="address-level2"
                                   placeholder="Colombo" 
                                   class="w-full h-11 sm:h-12 px-3.5 sm:px-4 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs">
                            @error('district')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Delivery Address *</label>
                            <textarea name="address" 
                                      rows="3" 
                                      required 
                                      autocomplete="street-address"
                                      placeholder="House/Apartment number, street name, town"
                                      class="w-full px-3.5 sm:px-4 py-2.5 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs resize-none">{{ old('address') }}</textarea>
                            @error('address')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Delivery Date (Optional)</label>
                            <input type="date" 
                                   name="delivery_date" 
                                   value="{{ old('delivery_date') }}" 
                                   min="{{ now()->toDateString() }}" 
                                   class="w-full h-11 sm:h-12 min-h-[2.75rem] px-3.5 sm:px-4 rounded-xl border border-stone-300 bg-white text-stone-800 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs">
                            @error('delivery_date')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Delivery Time (Optional)</label>
                            <input type="time" 
                                   name="delivery_time" 
                                   value="{{ old('delivery_time') }}" 
                                   class="w-full h-11 sm:h-12 min-h-[2.75rem] px-3.5 sm:px-4 rounded-xl border border-stone-300 bg-white text-stone-800 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs">
                            @error('delivery_time')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1.5">Special Note / Card Greeting (Optional)</label>
                            <textarea name="special_note" 
                                      rows="3" 
                                      class="w-full px-3.5 sm:px-4 py-2.5 rounded-xl border border-stone-300 bg-white text-stone-800 placeholder-stone-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition text-base sm:text-sm shadow-2xs resize-none" 
                                      placeholder="Any special instructions for delivery, recipient contact, or greeting message...">{{ old('special_note') }}</textarea>
                            @error('special_note')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Payment Method --}}
            <div class="rounded-2xl sm:rounded-3xl bg-white border border-stone-200 shadow-soft p-4 sm:p-6 md:p-7">
                <h2 class="font-display text-base sm:text-lg font-semibold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-primary-600 text-sm font-bold">2</span>
                    Payment Method
                </h2>

                <div class="space-y-3">
                    <label class="group relative flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 sm:p-4 rounded-2xl border-2 border-stone-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50/30 transition-all duration-200 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 shadow-2xs">
                        <div class="flex items-start sm:items-center gap-3">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="online" 
                                   x-model="paymentMethod" 
                                   {{ old('payment_method', 'online') === 'online' ? 'checked' : '' }} 
                                   class="size-4 sm:size-4.5 text-primary-600 focus:ring-primary-500 mt-1 sm:mt-0 shrink-0">
                            <div>
                                <span class="font-semibold text-stone-800 text-sm sm:text-base block">Credit / Debit Card</span>
                                <span class="text-xs text-stone-500 block mt-0.5">Instant & secure payment via PayHere (Visa, Mastercard, Amex)</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 pl-7 sm:pl-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold bg-blue-100 text-blue-700">VISA</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold bg-amber-100 text-amber-800">Mastercard</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold bg-emerald-100 text-emerald-800">PayHere</span>
                        </div>
                    </label>

                    <label class="group relative flex items-start sm:items-center gap-3 p-3.5 sm:p-4 rounded-2xl border-2 border-stone-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50/30 transition-all duration-200 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 shadow-2xs">
                        <input type="radio" 
                               name="payment_method" 
                               value="cash_on_delivery" 
                               x-model="paymentMethod" 
                               {{ old('payment_method') === 'cash_on_delivery' ? 'checked' : '' }} 
                               class="size-4 sm:size-4.5 text-primary-600 focus:ring-primary-500 mt-1 sm:mt-0 shrink-0">
                        <div>
                            <span class="font-semibold text-stone-800 text-sm sm:text-base block">Bank Transfer</span>
                            <span class="text-xs text-stone-500 block mt-0.5">Pay via bank app/CDM and share your deposit slip on WhatsApp</span>
                        </div>
                    </label>
                </div>
                @error('payment_method')<p class="text-danger-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror

                <div x-show="paymentMethod === 'online'" x-transition class="mt-4 p-3.5 sm:p-4 rounded-2xl border border-primary-200 bg-primary-50/70 text-xs text-primary-950 flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-stone-800 text-xs sm:text-sm">Bank-grade Secure Card Checkout</p>
                        <p class="text-stone-600 mt-0.5 leading-relaxed">When you click "Place Order", you will be securely redirected to PayHere to complete payment with 3D Secure OTP verification.</p>
                    </div>
                </div>
            </div>

            {{-- Bank Transfer Box --}}
            @php
                $bankTransfer = config('shop.bank_transfer', [
                    'bank' => 'NDB Bank',
                    'branch' => 'Rathmalana',
                    'account_name' => 'Southern Turn Networks',
                    'account_number' => '115511287806',
                ]);
                $slipPhone = config('contact.phone');
                $slipPhoneDigits = preg_replace('/\D+/', '', $slipPhone);
            @endphp
            <div class="rounded-2xl sm:rounded-3xl bg-white border border-stone-200 shadow-soft p-4 sm:p-6 md:p-7" 
                 x-show="paymentMethod === 'cash_on_delivery'" 
                 x-transition 
                 x-data="{ copied: false }">
                <h2 class="font-display text-base sm:text-lg font-semibold text-stone-800 mb-3 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-sm font-bold" aria-hidden="true">₨</span>
                    Bank Transfer Details
                </h2>
                <p class="text-xs sm:text-sm text-stone-600 mb-4 leading-relaxed">Please make payment to our bank account below. After completing the transfer, send us the payment slip via WhatsApp.</p>
                
                <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-3.5 sm:p-4 mb-4 space-y-2.5 text-xs sm:text-sm">
                    <div class="flex items-center justify-between py-1 border-b border-stone-200/60">
                        <span class="font-semibold text-stone-600">Bank</span>
                        <span class="text-stone-900 font-semibold">{{ $bankTransfer['bank'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-stone-200/60">
                        <span class="font-semibold text-stone-600">Branch</span>
                        <span class="text-stone-900 font-semibold">{{ $bankTransfer['branch'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-stone-200/60">
                        <span class="font-semibold text-stone-600">Account Name</span>
                        <span class="text-stone-900 font-semibold text-right">{{ $bankTransfer['account_name'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="font-semibold text-stone-600">Account Number</span>
                        <div class="flex items-center gap-2">
                            <span class="text-stone-900 font-mono font-bold tracking-wide text-sm sm:text-base">{{ $bankTransfer['account_number'] }}</span>
                            <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ $bankTransfer['account_number'] }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-200/80 hover:bg-stone-300/80 text-stone-700 text-xs font-medium transition active:scale-95" 
                                    title="Copy account number">
                                <span x-show="!copied" class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Copy</span>
                                </span>
                                <span x-show="copied" x-cloak class="text-emerald-700 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Copied!</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200/90 bg-amber-50 p-3.5 sm:p-4 text-xs sm:text-sm text-amber-950">
                    <p class="font-bold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Send your transfer slip
                    </p>
                    <p class="text-amber-900/90 leading-relaxed">Share a clear screenshot of your payment slip to <span class="font-semibold whitespace-nowrap">{{ $slipPhone }}</span> on WhatsApp so we can confirm and dispatch your order promptly.</p>
                    @if(strlen($slipPhoneDigits) >= 9)
                        <a href="https://wa.me/{{ $slipPhoneDigits }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 mt-3 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 active:scale-95 transition-all shadow-sm">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>Open WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order Summary (Right on desktop, Step 3 on mobile) --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl sm:rounded-3xl bg-white border border-stone-200 shadow-soft p-4 sm:p-5 md:p-6 lg:sticky lg:top-24">
                <div class="rounded-2xl bg-gradient-to-r from-primary-50 to-emerald-50 border border-primary-100 px-4 py-3 mb-4 sm:mb-5">
                    <h2 class="font-display text-base sm:text-lg font-bold text-stone-800 flex items-center justify-between">
                        <span>Order Summary</span>
                        <span class="text-xs font-semibold text-primary-700 bg-white/90 border border-primary-200 rounded-full px-2.5 py-0.5">{{ $items->sum('quantity') }} {{ Str::plural('item', $items->sum('quantity')) }}</span>
                    </h2>
                    <p class="text-xs text-stone-600 mt-0.5">Review items and total before placing order.</p>
                </div>

                {{-- Items list with thumbnails --}}
                <ul class="space-y-3 max-h-72 overflow-y-auto pr-1">
                    @foreach($items as $item)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-11 h-11 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/80 shrink-0 flex items-center justify-center">
                                    @if($item['product'] && $item['product']->images->isNotEmpty())
                                        <img src="{{ $item['product']->images->first()->public_url }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-1.5" aria-hidden="true">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-stone-800 font-medium text-xs sm:text-sm truncate" title="{{ $item['product']->name }}">{{ $item['product']->name }}</p>
                                    <p class="text-[11px] text-stone-500">Qty: {{ $item['quantity'] }}</p>
                                </div>
                            </div>
                            <span class="font-semibold text-stone-800 text-xs sm:text-sm shrink-0 ml-2">LKR {{ number_format($item['subtotal'], 0) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 pt-4 border-t border-stone-200 space-y-2 text-xs sm:text-sm">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($subtotal ?? $total, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Delivery charge</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($deliveryCharge ?? 0, 0) }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-stone-200 flex justify-between items-baseline font-display">
                    <div>
                        <span class="text-stone-800 font-bold text-base sm:text-lg">Total</span>
                        <span class="text-[11px] text-stone-500 block">Including delivery</span>
                    </div>
                    <span class="text-primary-700 font-bold text-xl sm:text-2xl">LKR {{ number_format($total, 0) }}</span>
                </div>

                <button type="submit" class="mt-5 w-full rounded-2xl bg-primary-600 hover:bg-primary-700 active:scale-[0.99] py-3.5 sm:py-4 px-4 text-white font-bold text-base shadow-soft hover:shadow-glow transition-all flex items-center justify-center gap-2 group">
                    <span>Place Order</span>
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>

                <a href="{{ route('cart.index') }}" class="flex items-center justify-center gap-1.5 mt-3 text-center text-xs sm:text-sm font-medium text-stone-500 hover:text-primary-600 transition-colors py-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Cart</span>
                </a>

                <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50/80 p-3 text-[11px] sm:text-xs text-emerald-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Your checkout is encrypted and personal details are kept safe.</span>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
