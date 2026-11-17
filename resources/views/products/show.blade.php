@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', $metaDescription ?? null)
@section('meta_image', $metaImage ?? null)
@section('canonical', $canonical ?? null)
@section('og_type', 'product')

@include('products.partials.structured-data-product')

@section('content')
@php
    $mainImage = $product->images->first();
    $mainImageUrl = $mainImage?->public_url;
    $avgRating = $product->reviews->isNotEmpty() ? round($product->reviews->avg('rating'), 1) : null;
    $reviewCount = $product->reviews->count();
    $categoryName = $product->category->name ?? 'Signature Collection';
    $discountPercent = $product->discount_price && $product->price > 0
        ? round((1 - $product->effective_price / $product->price) * 100)
        : null;
    $shortDescription = \Illuminate\Support\Str::limit(
        strip_tags($product->description ?? 'Thoughtfully curated gifting made for unforgettable celebrations.'),
        180
    );
    if ($product->delivery_type === 'one_to_three_days') {
        $deliveryLabel = '1 - 3 days';
    } elseif ($product->delivery_type === 'three_to_five_days') {
        $deliveryLabel = '3 - 5 days';
    } else {
        $deliveryLabel = $product->delivery_type
            ? \Illuminate\Support\Str::of($product->delivery_type)->replace('_', ' ')->title()
            : 'Colombo area';
    }
@endphp
<div x-data="productDetail({{ $product->id }})" class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10 max-md:px-3 max-md:py-3 max-md:pb-24">
    <div class="absolute inset-x-0 top-0 -z-10 hidden h-[44rem] bg-[radial-gradient(circle_at_top_left,rgba(251,191,36,0.14),transparent_34%),radial-gradient(circle_at_top_right,rgba(16,185,129,0.14),transparent_30%),linear-gradient(180deg,#fffbf2_0%,rgba(255,255,255,0)_90%)] lg:block"></div>
    <div class="absolute inset-x-0 top-0 -z-10 h-[28rem] bg-[radial-gradient(circle_at_top_left,rgba(251,191,36,0.18),transparent_36%),radial-gradient(circle_at_top_right,rgba(16,185,129,0.16),transparent_34%),linear-gradient(180deg,#fffbf2_0%,rgba(255,255,255,0)_92%)] lg:hidden"></div>

    <!-- <nav class="mb-8 hidden lg:flex" aria-label="Breadcrumb">
        <div class="inline-flex items-center gap-2 rounded-full border border-white/70 bg-white/80 px-4 py-2 text-sm text-stone-500 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] backdrop-blur">
            <a href="{{ route('home') }}" class="transition-colors hover:text-primary-600">Home</a>
            <span>/</span>
            <a href="{{ route('shop.index') }}" class="transition-colors hover:text-primary-600">Shop</a>
            <span>/</span>
            <span class="max-w-[24rem] truncate font-medium text-stone-800">{{ $product->name }}</span>
        </div>
    </nav> -->

    <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(30rem,34rem)] lg:gap-12 max-md:gap-4">
        <div
            class="space-y-4 lg:space-y-0 max-md:space-y-3"
            x-data="galleryCarousel({{ $product->images->count() }})"
        >
            <div class="space-y-4 max-md:space-y-3">
                <div>
                    @if($product->images->isEmpty())
                        <div class="relative overflow-hidden rounded-[1.8rem] border border-stone-200 bg-white shadow-[0_24px_50px_-38px_rgba(15,23,42,0.35)] max-md:rounded-[1.4rem]">
                            <div class="grid w-full place-items-center rounded-[1.25rem] bg-stone-50" style="aspect-ratio: 2 / 3;">
                                <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="block h-full w-full object-contain p-4 opacity-40 grayscale" aria-hidden="true">
                            </div>
                        </div>
                    @else
                        <div class="group relative overflow-hidden rounded-[1.8rem] border border-stone-200 bg-white shadow-[0_24px_50px_-38px_rgba(15,23,42,0.35)] transition-shadow duration-300 hover:shadow-[0_30px_70px_-40px_rgba(15,23,42,0.4)] max-md:rounded-[1.4rem]">
                            <div
                                class="relative w-full overflow-hidden rounded-[1.25rem] bg-stone-50"
                                style="touch-action: pan-y; aspect-ratio: 2 / 3;"
                            >
                                @foreach($product->images as $i => $img)
                                    <div
                                        x-show="current === {{ $i }} || (typeof current === 'undefined' && {{ $i }} === 0)"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        class="absolute inset-0 grid h-full w-full place-items-center text-center"
                                        @if($i > 0) style="display:none;" @endif
                                    >
                                        <img src="{{ $img->public_url }}" alt="{{ $product->name }}" class="block h-full w-full object-contain object-center transition-transform duration-500 ease-out group-hover:scale-105">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if($product->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2 max-md:gap-2 max-md:pb-1">
                        @foreach($product->images as $i => $img)
                            <button
                                type="button"
                                @click="current = {{ $i }}"
                                class="group relative w-14 flex-shrink-0 overflow-hidden rounded-[1.1rem] border bg-white p-2 transition-all duration-300 lg:w-20 max-md:w-12 max-md:rounded-[0.9rem]"
                                style="aspect-ratio: 2 / 3;"
                                :class="current === {{ $i }} ? 'border-stone-900 shadow-[0_18px_35px_-26px_rgba(15,23,42,0.35)]' : 'border-stone-200 hover:border-stone-300'"
                            >
                                <img src="{{ $img->public_url }}" alt="" class="h-full w-full rounded-[0.8rem] object-cover transition-transform duration-300 ease-out group-hover:scale-105">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="max-md:-mt-0.5 lg:sticky lg:top-28">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/85 shadow-[0_30px_80px_-35px_rgba(15,23,42,0.35)] backdrop-blur max-md:rounded-[1.7rem]">
                <div class="border-b border-stone-200/70 bg-[linear-gradient(135deg,rgba(255,248,234,0.95)_0%,rgba(255,255,255,0.9)_48%,rgba(238,251,246,0.92)_100%)] px-5 py-5 lg:px-7 lg:py-6 max-md:px-4 max-md:py-4">
                    <div class="flex flex-wrap items-center gap-2 max-md:gap-1.5">
                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-700 max-md:px-2.5 max-md:text-[10px]">Premium Selection</span>
                        <span class="inline-flex items-center rounded-full border border-stone-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-600 max-md:px-2.5 max-md:text-[10px]">{{ $categoryName }}</span>
                        @if($avgRating !== null)
                            <span class="inline-flex items-center gap-1 rounded-full border border-white/70 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-700 max-md:px-2.5 max-md:text-[10px]">
                                <svg class="h-3.5 w-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ $avgRating }} / 5
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-3 font-display text-2xl font-bold leading-tight text-stone-900 lg:text-[2.6rem] lg:leading-[1.1] max-md:text-[1.6rem] max-md:leading-[1.15]">{{ $product->name }}</h1>
                    <p class="mt-3 max-w-2xl text-[13px] leading-6 text-stone-600 lg:text-[15px] lg:leading-7 max-md:text-[13px] max-md:leading-5">{{ $shortDescription }}</p>
                </div>

                <div class="px-5 py-5 lg:px-7 lg:py-6 max-md:px-4 max-md:py-4">
                    <div class="rounded-[1.75rem] border border-stone-200/80 bg-[linear-gradient(135deg,#1c1917_0%,#292524_55%,#0f766e_100%)] p-5 text-white shadow-[0_35px_70px_-40px_rgba(15,23,42,0.85)] lg:p-6 max-md:rounded-[1.45rem] max-md:p-4">
                        <div class="flex flex-wrap items-start justify-between gap-4 max-md:gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-white/60">Exclusive Price</p>
                                <div class="mt-2 flex flex-wrap items-end gap-3 max-md:gap-2">
                                    <span class="text-[2rem] font-bold tracking-tight lg:text-[2.9rem] max-md:text-[1.9rem]">LKR {{ number_format($product->effective_price, 0) }}</span>
                                    @if($product->discount_price)
                                        <span class="pb-1 text-sm font-medium text-white/45 line-through lg:text-base max-md:text-xs">LKR {{ number_format($product->price, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($discountPercent)
                                <div class="rounded-[1.15rem] border border-amber-300/20 bg-white/10 px-3.5 py-2.5 text-right backdrop-blur max-md:px-3 max-md:py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200/90 max-md:text-[10px]">Seasonal Saving</p>
                                    <p class="mt-1 text-xl font-bold text-amber-300 lg:text-2xl max-md:text-lg">{{ $discountPercent }}%</p>
                                </div>
                            @endif
                        </div>

                        @if($avgRating !== null)
                            <div class="mt-4 flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= round($avgRating) ? 'text-amber-300' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="ml-2 text-xs font-medium text-white/75 lg:text-sm">{{ $avgRating }} from {{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</span>
                            </div>
                        @endif

                        <p class="mt-4 max-w-2xl text-[13px] leading-6 text-white/70 lg:text-sm lg:leading-6 max-md:text-[12px] max-md:leading-5">
                            Elegant presentation, secure packaging, and curated finishing touches make this a standout gift for special moments.
                        </p>

                        @if($product->stock > 0)
                            <form
                                action="{{ route('cart.add', $product) }}"
                                method="POST"
                                class="add-to-cart-form mt-7"
                                id="add-to-cart-form"
                                x-data="{ qty: 1, min: 1, max: {{ max(1, (int) $product->stock) }} }"
                                data-product-id="{{ $product->id }}"
                                data-image-url="{{ $mainImageUrl }}"
                                data-product-name="{{ $product->name }}"
                            >
                                @csrf

                                <div class="space-y-3 max-md:space-y-2.5">
                                    <div class="flex justify-start lg:justify-end max-md:justify-stretch">
                                        <div class="mt-3 flex items-center rounded-[1.1rem] border border-white/10 bg-white/10 p-1.5 max-md:mt-2 max-md:w-full">
                                            <button
                                                type="button"
                                                @click="qty = Math.max(min, qty - 1)"
                                                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-xl font-semibold text-white transition hover:bg-white/15 max-md:h-10 max-md:w-10"
                                                aria-label="Decrease quantity"
                                            >
                                                -
                                            </button>
                                            <input
                                                type="number"
                                                name="quantity"
                                                x-model="qty"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                @input="qty = Math.max(min, Math.min(max, parseInt($event.target.value || min)))"
                                                class="w-full border-0 bg-transparent px-3 text-center text-lg font-bold text-white focus:ring-0 max-md:text-base"
                                            >
                                            <button
                                                type="button"
                                                @click="qty = Math.min(max, qty + 1)"
                                                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-xl font-semibold text-white transition hover:bg-white/15 max-md:h-10 max-md:w-10"
                                                aria-label="Increase quantity"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2 max-md:gap-2.5">
                                        <button type="submit" class="add-to-cart-btn inline-flex min-h-[3.35rem] items-center justify-center rounded-[1.15rem] border border-white/15 bg-white/10 px-5 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/16 hover:shadow-[0_20px_35px_-24px_rgba(255,255,255,0.45)] max-md:min-h-[3.1rem] max-md:rounded-[1rem] max-md:px-4 max-md:py-3">
                                            Add to Cart
                                        </button>
                                        <button type="submit" name="buy_now" value="1" class="buy-now-btn inline-flex min-h-[3.35rem] items-center justify-center rounded-[1.15rem] bg-[linear-gradient(135deg,#fde68a_0%,#f59e0b_100%)] px-5 py-3.5 text-sm font-semibold text-stone-950 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_24px_40px_-24px_rgba(245,158,11,0.7)] max-md:min-h-[3.1rem] max-md:rounded-[1rem] max-md:px-4 max-md:py-3">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="mt-7 rounded-[1.5rem] border border-danger-300/25 bg-danger-500/10 px-5 py-4 text-sm font-medium text-danger-100">
                                This item is currently unavailable. Contact us if you need a similar premium arrangement for your event.
                            </div>
                        @endif
                    </div>

                    @auth
                        <div class="mt-4">
                            @if($inWishlist ?? false)
                                <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-warning-300/60 bg-warning-50 px-4 py-2 text-sm font-medium text-warning-700 transition hover:bg-warning-100">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        Remove from Wishlist
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
                <div class="mt-5 grid gap-2.5 grid-cols-2 sm:grid-cols-3">
                    <div class="rounded-[1.25rem] border border-white/80 bg-white/80 px-4 py-3.5 shadow-[0_12px_25px_-22px_rgba(15,23,42,0.45)] max-md:px-3.5 max-md:py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-400">Availability</p>
                        <p class="mt-1.5 text-sm font-semibold text-stone-900 lg:text-[15px]">{{ $product->stock > 0 ? $product->stock . ' ready now' : 'Made to order' }}</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-white/80 bg-white/80 px-4 py-3.5 shadow-[0_12px_25px_-22px_rgba(15,23,42,0.45)] max-md:px-3.5 max-md:py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-400">Delivery</p>
                        <p class="mt-1.5 text-sm font-semibold text-stone-900 lg:text-[15px]">{{ $deliveryLabel }}</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-white/80 bg-white/80 px-4 py-3.5 shadow-[0_12px_25px_-22px_rgba(15,23,42,0.45)] col-span-2 sm:col-span-1 max-md:px-3.5 max-md:py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-400">Presentation</p>
                        <p class="mt-1.5 text-sm font-semibold text-stone-900 lg:text-[15px]">Gift-ready finish</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3 max-md:mt-6 max-md:gap-3">
        <div class="rounded-[1.5rem] border border-stone-200/80 bg-white/88 p-5 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.35)] backdrop-blur max-md:rounded-[1.35rem] max-md:p-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-50 text-primary-600 shadow-sm max-md:h-10 max-md:w-10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <p class="mt-4 text-sm font-semibold text-stone-900 max-md:mt-3">Gift-ready packaging</p>
            <p class="mt-1 text-sm leading-6 text-stone-500 max-md:text-[13px] max-md:leading-5">Polished presentation designed to feel elevated from the moment it arrives.</p>
        </div>
        <div class="rounded-[1.5rem] border border-stone-200/80 bg-white/88 p-5 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.35)] backdrop-blur max-md:rounded-[1.35rem] max-md:p-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-50 text-primary-600 shadow-sm max-md:h-10 max-md:w-10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-4 text-sm font-semibold text-stone-900 max-md:mt-3">Personalised notes</p>
            <p class="mt-1 text-sm leading-6 text-stone-500 max-md:text-[13px] max-md:leading-5">Add thoughtful finishing details at checkout for a more memorable gift.</p>
        </div>
        <div class="rounded-[1.5rem] border border-stone-200/80 bg-white/88 p-5 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.35)] backdrop-blur md:col-span-2 xl:col-span-1 max-md:rounded-[1.35rem] max-md:p-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-50 text-primary-600 shadow-sm max-md:h-10 max-md:w-10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <p class="mt-4 text-sm font-semibold text-stone-900 max-md:mt-3">Handled with care</p>
            <p class="mt-1 text-sm leading-6 text-stone-500 max-md:text-[13px] max-md:leading-5">Secure wrapping and careful delivery support a premium receiving experience.</p>
        </div>
    </div>

    <div x-data="{ tab: 'details' }" class="mt-16 lg:mt-20 max-md:mt-8">
        <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/88 shadow-[0_30px_80px_-35px_rgba(15,23,42,0.28)] backdrop-blur max-md:rounded-[1.7rem]">
            <div class="border-b border-stone-200/80 px-4 py-4 lg:px-6 max-md:px-3 max-md:py-3">
                <div class="flex gap-2 overflow-x-auto scrollbar-hide max-md:gap-1.5">
                    <button type="button" @click="tab = 'details'" :class="tab === 'details' ? 'bg-stone-900 text-white shadow-[0_20px_35px_-24px_rgba(15,23,42,0.65)]' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'" class="flex-shrink-0 rounded-full px-5 py-3 text-sm font-semibold transition-colors max-md:min-h-[44px]">Details</button>
                    <button type="button" @click="tab = 'reviews'" :class="tab === 'reviews' ? 'bg-stone-900 text-white shadow-[0_20px_35px_-24px_rgba(15,23,42,0.65)]' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'" class="flex-shrink-0 rounded-full px-5 py-3 text-sm font-semibold transition-colors max-md:min-h-[44px]">Reviews</button>
                    <button type="button" @click="tab = 'shipping'" :class="tab === 'shipping' ? 'bg-stone-900 text-white shadow-[0_20px_35px_-24px_rgba(15,23,42,0.65)]' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'" class="flex-shrink-0 rounded-full px-5 py-3 text-sm font-semibold transition-colors max-md:min-h-[44px]">Shipping</button>
                </div>
            </div>

            <div class="px-5 py-6 lg:px-8 lg:py-8 max-md:px-4 max-md:py-4">
                <div x-show="tab === 'details'" x-transition class="grid gap-8 lg:grid-cols-[minmax(0,1.15fr)_22rem] max-md:gap-4">
                    <div class="rounded-[1.75rem] border border-stone-200/80 bg-[linear-gradient(180deg,rgba(255,255,255,0.92)_0%,rgba(248,250,252,0.95)_100%)] p-6 lg:p-8 max-md:rounded-[1.4rem] max-md:p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-stone-400">Product Story</p>
                        <h2 class="mt-3 font-display text-2xl font-semibold text-stone-900 max-md:text-xl">Designed to feel memorable before it is even opened.</h2>
                        <p class="mt-5 text-base leading-8 text-stone-600 max-md:mt-3 max-md:text-[14px] max-md:leading-6">{{ $product->description ?? 'No additional details.' }}</p>
                    </div>

                    <div class="space-y-4 max-md:space-y-3">
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Category</p>
                            <p class="mt-2 text-lg font-semibold text-stone-900 max-md:text-base">{{ $categoryName }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Occasion Ready</p>
                            <p class="mt-2 text-sm leading-6 text-stone-600 max-md:text-[13px] max-md:leading-5">A refined gifting option for celebrations, surprise deliveries, and premium event moments.</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Service Note</p>
                            <p class="mt-2 text-sm leading-6 text-stone-600 max-md:text-[13px] max-md:leading-5">Gift wrapping and personal messages are available during checkout to complete the presentation.</p>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'reviews'" x-transition class="space-y-6">
                    <div class="rounded-[1.75rem] border border-stone-200/80 bg-[linear-gradient(180deg,rgba(255,255,255,0.92)_0%,rgba(248,250,252,0.95)_100%)] p-6 lg:p-8 max-md:rounded-[1.4rem] max-md:p-4">
                        @if($product->reviews->isNotEmpty())
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Customer Reviews</p>
                                    <h2 class="mt-2 font-display text-2xl font-semibold text-stone-900 max-md:text-xl">What customers loved most</h2>
                                    <div class="mt-3 flex items-center gap-1.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-4 w-4 {{ $i <= round($avgRating ?? 0) ? 'text-amber-400' : 'text-stone-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-sm font-medium text-stone-500 max-md:text-[13px]">{{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</span>
                                    </div>
                                </div>
                                <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-center max-md:w-full max-md:rounded-[1.2rem] max-md:px-4 max-md:py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-700">Average Rating</p>
                                    <p class="mt-1 text-3xl font-bold text-stone-900 max-md:text-2xl">{{ number_format($avgRating ?? 0, 1) }}</p>
                                    <p class="mt-1 text-sm text-stone-500 max-md:text-[13px]">{{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</p>
                                </div>
                            </div>

                        @else
                            <div class="rounded-[1.5rem] border border-dashed border-stone-300 bg-white/80 px-6 py-8 text-center max-md:rounded-[1.2rem] max-md:px-4 max-md:py-6">
                                <div class="flex justify-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-5 w-5 text-stone-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <p class="mt-4 text-sm font-medium text-stone-700">No reviews yet.</p>
                                <p class="mt-1 text-sm text-stone-500">Be the first to share your experience with this product.</p>
                            </div>
                        @endif
                    </div>

                    @auth
                        <div class="rounded-[1.75rem] border border-stone-200/80 bg-stone-50/80 p-6 lg:p-8 max-md:rounded-[1.4rem] max-md:p-4" x-data="{ selectedRating: {{ (int) old('rating', 5) }} }">
                            <h3 class="font-display text-xl font-semibold text-stone-900 max-md:text-lg">Add your review</h3>
                            @if(session('success'))
                                <p class="mt-3 text-sm font-medium text-primary-600">{{ session('success') }}</p>
                            @endif
                            <form action="{{ route('review.store', $product) }}" method="POST" class="mt-5 space-y-4">
                                @csrf
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-stone-700">Your Rating</label>
                                    <input type="hidden" name="rating" :value="selectedRating">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <template x-for="star in 5" :key="star">
                                            <button
                                                type="button"
                                                @click="selectedRating = star"
                                                class="inline-flex h-11 w-11 items-center justify-center rounded-full border transition-all duration-200"
                                                :class="star <= selectedRating ? 'border-amber-300 bg-amber-50 text-amber-400 shadow-[0_10px_20px_-16px_rgba(245,158,11,0.8)]' : 'border-stone-200 bg-white text-stone-300 hover:border-amber-200 hover:text-amber-300'"
                                                :aria-label="`Rate ${star} stars`"
                                            >
                                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </button>
                                        </template>
                                        <span class="text-sm font-medium text-stone-500" x-text="selectedRating + ' / 5'"></span>
                                    </div>
                                    @error('rating')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="comment" class="mb-1 block text-sm font-semibold text-stone-700">Comment (optional)</label>
                                    <textarea name="comment" id="comment" rows="3" class="w-full rounded-2xl border-stone-200 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20" placeholder="Share your experience...">{{ old('comment') }}</textarea>
                                    @error('comment')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit" class="rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-800">Submit review</button>
                            </form>
                        </div>
                    @else
                        <p class="text-stone-500"><a href="{{ route('login') }}" class="font-medium text-primary-600 transition-colors hover:text-primary-700">Log in</a> to leave a review.</p>
                    @endauth
                </div>

                <div x-show="tab === 'shipping'" x-transition class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_20rem] max-md:gap-4">
                    <div class="rounded-[1.75rem] border border-stone-200/80 bg-[linear-gradient(180deg,rgba(255,255,255,0.92)_0%,rgba(248,250,252,0.95)_100%)] p-6 lg:p-8 max-md:rounded-[1.4rem] max-md:p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Shipping & Care</p>
                        <h2 class="mt-3 font-display text-2xl font-semibold text-stone-900 max-md:text-xl">Handled with detail from our studio to your recipient.</h2>
                        <p class="mt-5 text-base leading-8 text-stone-600 max-md:mt-3 max-md:text-[14px] max-md:leading-6">We deliver in Colombo area only. Delivery typically takes 3–7 business days. Free delivery on orders over LKR 5,000. Gift wrapping and personalised messages available at checkout.</p>
                    </div>
                    <div class="space-y-4 max-md:space-y-3">
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Coverage</p>
                            <p class="mt-2 text-lg font-semibold text-stone-900 max-md:text-base">Colombo only</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Lead time</p>
                            <p class="mt-2 text-lg font-semibold text-stone-900 max-md:text-base">3–7 business days</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-stone-200/80 bg-stone-50/80 p-5 max-md:rounded-[1.25rem] max-md:p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Free delivery</p>
                            <p class="mt-2 text-lg font-semibold text-stone-900 max-md:text-base">Orders over LKR 5,000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fly-to-cart" class="fixed z-[200] pointer-events-none opacity-0 transition-none" aria-hidden="true">
    <img src="" alt="" class="w-16 h-16 rounded-xl object-cover shadow-soft-lg border-2 border-white">
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('galleryCarousel', (count) => ({
        current: 0,
    }));
    Alpine.data('productDetail', (productId) => ({ productId }));
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.add-to-cart-form');
    const flyEl = document.getElementById('fly-to-cart');
    const cartIcon = document.querySelector('header [data-cart-trigger]');
    if (form && flyEl && cartIcon && form.querySelector('.add-to-cart-btn')) {
        form.addEventListener('submit', function(e) {
            const submitter = e.submitter;
            if (submitter?.classList.contains('buy-now-btn') || submitter?.name === 'buy_now') {
                return;
            }

            const imgUrl = this.dataset.imageUrl;
            if (!imgUrl) return;
            e.preventDefault();
            const img = flyEl.querySelector('img');
            img.src = imgUrl;
            const btn = form.querySelector('.add-to-cart-btn');
            const rect = btn.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();
            flyEl.style.left = rect.left + rect.width / 2 - 32 + 'px';
            flyEl.style.top = rect.top + rect.height / 2 - 32 + 'px';
            flyEl.style.width = '64px';
            flyEl.style.height = '64px';
            flyEl.style.opacity = '1';
            flyEl.classList.remove('opacity-0');
            flyEl.style.transition = 'none';
            requestAnimationFrame(() => {
                flyEl.style.transition = 'all 0.65s cubic-bezier(0.4, 0, 0.2, 1)';
                flyEl.style.left = cartRect.left + cartRect.width / 2 - 32 + 'px';
                flyEl.style.top = cartRect.top + cartRect.height / 2 - 32 + 'px';
                setTimeout(() => {
                    flyEl.style.opacity = '0';
                    setTimeout(() => form.submit(), 300);
                }, 550);
            });
        });
    }
});
</script>
@endsection
