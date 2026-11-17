@extends('layouts.app')

@section('title', 'Checkout')
@section('noindex', '1')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
    <section class="relative overflow-hidden rounded-3xl border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-emerald-50/70 p-6 md:p-8 mb-8 md:mb-10">
        <div class="absolute -top-24 -right-16 h-56 w-56 rounded-full bg-primary-200/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-10 h-48 w-48 rounded-full bg-emerald-200/40 blur-3xl pointer-events-none"></div>
        <div class="relative grid grid-cols-1 lg:grid-cols-5 gap-4 lg:gap-5 items-end">
            <div class="lg:col-span-2">
                <p class="inline-flex items-center rounded-full border border-primary-200 bg-white/90 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-primary-700 mb-2">
                    Secure checkout
                </p>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-stone-800 mb-1.5 leading-tight">Complete your order</h1>
                <p class="text-sm text-stone-600">Enter delivery details, confirm payment, and place your order safely.</p>
            </div>

            <div class="lg:col-span-3 rounded-2xl border border-primary-200/70 bg-white/80 backdrop-blur-sm p-3 md:p-4">
                <div class="grid grid-cols-3 gap-2">
                    <div class="relative text-center">
                        <div class="absolute top-5 left-[55%] right-0 h-[2px] bg-primary-200"></div>
                        <span class="relative z-10 mx-auto flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-bold shadow-sm">1</span>
                        <p class="mt-1.5 text-xs font-semibold text-stone-700">Delivery</p>
                    </div>
                    <div class="relative text-center">
                        <div class="absolute top-5 left-[55%] right-0 h-[2px] bg-primary-200"></div>
                        <span class="relative z-10 mx-auto flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-bold shadow-sm">2</span>
                        <p class="mt-1.5 text-xs font-semibold text-stone-700">Payment</p>
                    </div>
                    <div class="text-center">
                        <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-bold shadow-sm">3</span>
                        <p class="mt-1.5 text-xs font-semibold text-stone-700">Review</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        @csrf
        <input type="hidden" name="checkout_token" value="{{ session('checkout_token') }}">
        @error('cart')
            <div class="lg:col-span-3 rounded-2xl bg-danger-50 border border-danger-200 text-danger-700 px-5 py-4 font-medium">{{ $message }}</div>
        @enderror

        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl bg-white border border-stone-200 shadow-soft p-4 md:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-primary-700 text-sm font-bold">1</span>
                        <div>
                            <h2 class="font-display text-lg font-semibold text-stone-800 leading-tight">Contact & Delivery</h2>
                            <p class="text-xs text-stone-500">Where and when we should deliver your order</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-stone-500 rounded-full border border-stone-200 bg-stone-50 px-2.5 py-1">* Required</span>
                </div>

                <div class="rounded-2xl border border-stone-100 bg-stone-50/70 p-3 md:p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Full Name *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', auth()->user()?->name) }}" required class="input-soft w-full bg-white">
                            @error('full_name')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Phone *</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required class="input-soft w-full bg-white">
                            @error('phone')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1">District *</label>
                            <input type="text" name="district" value="{{ old('district', 'Colombo') }}" required class="input-soft w-full bg-white" placeholder="Colombo">
                            @error('district')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Address *</label>
                            <textarea name="address" rows="3" required class="input-soft w-full resize-none bg-white">{{ old('address') }}</textarea>
                            @error('address')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Delivery Date (Optional)</label>
                            <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" min="{{ now()->toDateString() }}" class="input-soft w-full bg-white">
                            @error('delivery_date')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Delivery Time (Optional)</label>
                            <input type="time" name="delivery_time" value="{{ old('delivery_time') }}" class="input-soft w-full bg-white">
                            @error('delivery_time')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-stone-700 mb-1">Special Note (Optional)</label>
                            <textarea name="special_note" rows="3" class="input-soft w-full resize-none bg-white" placeholder="Any special instructions for this order...">{{ old('special_note') }}</textarea>
                            @error('special_note')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white border border-stone-200 shadow-soft p-5 md:p-7">
                <h2 class="font-display text-lg font-semibold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-primary-600 text-sm font-bold">2</span>
                    Payment Method
                </h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-stone-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50/30 transition-all duration-300 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                        <input type="radio" name="payment_method" value="cash_on_delivery" {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'checked' : '' }} class="text-primary-500 focus:ring-primary-500">
                        <div>
                            <span class="font-semibold text-stone-800 block">Bank transfer</span>
                            <span class="text-xs text-stone-500">Pay now and share your transfer slip on WhatsApp</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-stone-200 bg-stone-100/80 cursor-not-allowed opacity-60 transition-all duration-300 select-none">
                        <input type="radio" name="payment_method" value="online" disabled class="text-primary-500 focus:ring-primary-500 cursor-not-allowed">
                        <span class="font-semibold text-stone-500">Online Payment</span>
                        <span class="text-xs text-stone-400">(Coming soon)</span>
                    </label>
                </div>
                @error('payment_method')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

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
            <div class="rounded-3xl bg-white border border-stone-200 shadow-soft p-5 md:p-7">
                <h2 class="font-display text-lg font-semibold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-sm font-bold" aria-hidden="true">₨</span>
                    Bank transfer
                </h2>
                <p class="text-sm text-stone-600 mb-4">Use these details if you pay by bank transfer. After you pay, send us your transfer slip so we can confirm your order.</p>
                <dl class="space-y-3 text-sm border border-stone-100 rounded-2xl bg-stone-50/80 p-4 mb-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:gap-4">
                        <dt class="font-semibold text-stone-600 shrink-0">Bank</dt>
                        <dd class="text-stone-900 font-medium text-right sm:text-left">{{ $bankTransfer['bank'] }}</dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:gap-4">
                        <dt class="font-semibold text-stone-600 shrink-0">Branch</dt>
                        <dd class="text-stone-900 font-medium text-right sm:text-left">{{ $bankTransfer['branch'] }}</dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:gap-4">
                        <dt class="font-semibold text-stone-600 shrink-0">Account name</dt>
                        <dd class="text-stone-900 font-medium text-right sm:text-left">{{ $bankTransfer['account_name'] }}</dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:gap-4">
                        <dt class="font-semibold text-stone-600 shrink-0">Account number</dt>
                        <dd class="text-stone-900 font-mono font-semibold tracking-wide text-right sm:text-left">{{ $bankTransfer['account_number'] }}</dd>
                    </div>
                </dl>
                <div class="rounded-2xl border border-amber-200/90 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                    <p class="font-semibold mb-1">Send your transfer slip</p>
                    <p class="text-amber-900/90">Share a clear screenshot of your payment slip to <span class="font-semibold whitespace-nowrap">{{ $slipPhone }}</span> on WhatsApp so we can verify your order quickly.</p>
                    @if(strlen($slipPhoneDigits) >= 9)
                        <a href="https://wa.me/{{ $slipPhoneDigits }}" target="_blank" rel="noopener noreferrer" class="inline-flex mt-3 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">Open WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order summary (right) --}}
        <div class="lg:col-span-1">
            <div class="rounded-3xl bg-white border border-stone-200 shadow-soft p-5 md:p-6 sticky top-24">
                <div class="rounded-2xl bg-primary-50 border border-primary-100 px-4 py-3 mb-5">
                    <h2 class="font-display text-lg font-semibold text-stone-800">Order Summary</h2>
                    <p class="text-xs text-stone-600 mt-1">Review items and total before placing order.</p>
                </div>
                <ul class="space-y-3">
                    @foreach($items as $item)
                        <li class="flex justify-between text-sm gap-3">
                            <span class="text-stone-700 line-clamp-2">{{ $item['product']->name }} <span class="text-stone-500">× {{ $item['quantity'] }}</span></span>
                            <span class="font-semibold text-stone-800 shrink-0 ml-2">LKR {{ number_format($item['subtotal'], 0) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 pt-4 border-t border-stone-200 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-600">Subtotal</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($subtotal ?? $total, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-600">Delivery charge</span>
                        <span class="font-semibold text-stone-800">LKR {{ number_format($deliveryCharge ?? 0, 0) }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-stone-200 flex justify-between font-display font-bold text-stone-800">
                    <span>Total</span>
                    <span class="text-primary-700 text-xl">LKR {{ number_format($total, 0) }}</span>
                </div>
                <button type="submit" class="mt-6 w-full rounded-2xl bg-primary-500 px-4 py-3.5 text-white font-bold text-base hover:bg-primary-600 shadow-soft transition-all duration-300">Place Order</button>
                <a href="{{ route('cart.index') }}" class="block mt-3 text-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">Back to Cart</a>
                <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-3 py-2.5 text-xs text-emerald-800">
                    Your checkout is secured and your order details are safely processed.
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
