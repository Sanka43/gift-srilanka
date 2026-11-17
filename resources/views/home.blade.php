@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', $seoDescription ?? config('seo.default_description'))
@section('meta_image', $seoImage ?? asset(config('seo.default_image')))
@section('fullBleed', true)

@section('content')
@php
    /** Hero crossfade: slides + dots both derive from this list (keep in sync). */
    $heroBackgroundImages = [
        'images/homebg.webp',
        'images/homebg1.webp',
        'images/homebg2.webp',
        'images/homebg3.webp',
        'images/homebg4.webp',
    ];
@endphp
<div class="relative z-[2]">
{{-- Hero: bg image lowest, dark overlay middle (light opacity so image visible), content highest; extends under navbar. Mobile: redesigned layout and typography (max-md only). --}}
{{-- Hero dots follow $heroBackgroundImages; if production looks wrong, redeploy and run: php artisan view:clear --}}
<section class="relative min-h-[58vh] max-md:min-h-[44vh] max-md:flex max-md:items-end flex items-center overflow-hidden">
    <div class="hero-bg-slides absolute inset-0 z-10 overflow-hidden" aria-hidden="true">
        @foreach ($heroBackgroundImages as $heroBgPath)
            <div class="hero-bg-slide bg-cover bg-center bg-no-repeat max-md:bg-[position:50%_30%]"
                 style="background-image: url('{{ asset($heroBgPath) }}');"></div>
        @endforeach
    </div>
    <div class="absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-1/4 right-0 w-96 h-96 rounded-full bg-primary-200/10 blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-1/4 left-0 w-80 h-80 rounded-full bg-amber-200/10 blur-3xl translate-y-1/2"></div>
    </div>
    {{-- Soft gradient on left for text contrast --}}
    <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-r from-stone-900/70 via-stone-900/20 to-transparent" aria-hidden="true"></div>
    {{-- Mobile only: stronger bottom gradient for content legibility when content is bottom-aligned --}}
    <div class="absolute inset-0 z-10 pointer-events-none hidden max-md:block bg-gradient-to-t from-black/45 from-20% via-black/50 via-50% to-transparent" aria-hidden="true"></div>
    <div class="relative z-20 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-24 pb-20 lg:pt-28 lg:pb-24 max-md:min-h-full max-md:pt-4 max-md:pb-0 max-md:mb-6 max-md:px-5 max-md:w-full max-md:flex max-md:flex-col max-md:items-center max-md:justify-end">
        <div class="hero-content-inner max-w-2xl max-md:max-w-[min(100%,22rem)] max-md:mx-auto max-md:w-full max-md:flex max-md:flex-col max-md:items-center max-md:text-center">
            <p class="hero-anim-item text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase text-amber-200/90 mb-4 max-md:hidden">Premium Gifts for Every Occasion</p>
            <h1 class="hero-anim-item hero-anim-delay-1 font-display text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold leading-[1.08] tracking-tight text-white drop-shadow-lg max-md:text-[1.75rem] max-md:leading-tight max-md:tracking-tight max-md:text-center">
                Season of Joy<br class="max-md:block sm:hidden"><br class="hidden sm:block"> of Gift Giving
            </h1>
            <p class="hero-anim-item hero-anim-delay-2 !hidden md:!block mt-6 text-base sm:text-lg text-stone-200/95 max-w-lg leading-relaxed">
                We deliver happiness across Colombo. Flowers, cakes, hampers & more—for birthdays, anniversaries & every celebration.
            </p>
            <p class="hero-anim-item hero-anim-delay-3 mt-4 text-sm text-stone-300 max-md:hidden">
                Birthday · Anniversary · Wedding · Corporate · Same-day delivery
            </p>
            <div class="hero-anim-item hero-anim-delay-4 mt-10 flex flex-wrap gap-4 max-md:mt-6 max-md:gap-3 max-md:w-full max-md:justify-center max-md:items-center max-md:px-1">
                <a href="{{ route('shop.index') }}" class="group/btn inline-flex items-center justify-center gap-2 px-6 py-3 max-md:px-5 max-md:py-2.5 rounded-xl text-base font-semibold bg-primary-500 text-white shadow-lg shadow-primary-500/25 hover:bg-primary-600 hover:shadow-xl hover:shadow-primary-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 min-h-[2.5rem] focus:outline-none focus:ring-2 focus:ring-white/40 focus:ring-offset-2 focus:ring-offset-stone-900/50 whitespace-nowrap">
                    <span>Shop now</span>
                    <svg class="w-4 h-4 transition-transform duration-300 ease-out group-hover/btn:translate-x-0.5 group-hover/btn:scale-110 group-hover/btn:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                </a>
            </div>
        </div>
    </div>
    <div class="hero-slide-dots absolute bottom-5 left-1/2 z-30 flex -translate-x-1/2 items-center gap-2 pointer-events-none max-md:bottom-4" role="presentation" aria-hidden="true">
        @foreach ($heroBackgroundImages as $_)
            <span class="hero-slide-dot"></span>
        @endforeach
    </div>
</section>
    
{{-- Featured Categories --}}
@if($categories->isNotEmpty())
{{-- Mobile only: Popular filters – preserve original mobile design --}}
<section class="md:hidden relative z-10 pt-0 pb-4 bg-stone-50" aria-labelledby="categories-heading-mobile"
         x-data="{ categoriesVisible: false }"
         x-intersect.once="categoriesVisible = true">
    <div class="max-w-7xl mx-auto px-4">
        <h2 id="categories-heading-mobile" class="sr-only">Popular filters</h2>
        <div class="flex flex-nowrap overflow-x-auto overflow-y-hidden -mx-4 scrollbar-hide border border-stone-300 bg-white shadow-sm scroll-smooth snap-x snap-mandatory touch-pan-x transition-all duration-700 ease-out"
             :class="categoriesVisible ? 'translate-x-0 opacity-100' : 'translate-x-[-28px] opacity-70'">
            @foreach($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                   class="flex-shrink-0 w-28 min-h-[3.5rem] flex flex-col justify-center px-3 py-3 bg-white text-stone-800 font-semibold text-sm uppercase tracking-wide border-r border-stone-200 last:border-r-0 snap-start transition-colors duration-200 hover:bg-primary-50/50 active:bg-stone-100">
                    <span class="min-w-0 w-full text-center line-clamp-2 leading-snug font-display tracking-tight">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Desktop only: redesigned category showcase cards --}}
<section class="hidden md:block relative py-12 lg:py-16 overflow-hidden bg-stone-100" aria-labelledby="categories-heading">
    <div class="absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-1/2 right-0 w-96 h-64 -translate-y-1/2 rounded-full bg-[#10B981]/[0.06] blur-3xl"></div>
        <div class="absolute top-1/2 left-0 w-80 h-48 -translate-y-1/2 rounded-full bg-[#FBBF24]/[0.08] blur-3xl"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-8 lg:mb-10">
            <p class="text-xs sm:text-sm font-semibold text-stone-500 uppercase tracking-[0.2em] mb-1">Browse</p>
            <h2 id="categories-heading" class="font-display text-2xl sm:text-3xl lg:text-3xl font-bold text-stone-800 tracking-tight">Shop by Category</h2>
            <span class="inline-block mt-2 h-0.5 w-12 rounded-full bg-primary-500" aria-hidden="true"></span>
        </header>
        {{-- Single row: fixed-width cards + seamless infinite auto-scroll (duplicated strip) --}}
        <div class="category-showcase-marquee pb-1" role="region" aria-label="Shop by category">
            <div class="category-showcase-marquee__track flex flex-nowrap gap-4 w-max pr-4">
                @foreach ([false, true] as $marqueeDuplicate)
                    @foreach($categories as $category)
                        @php $imgUrl = $category->image_url; @endphp
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                           class="category-showcase-card group h-[210px] sm:h-[230px] lg:h-[220px] flex-shrink-0 w-[196px] min-w-[196px] max-w-[196px] snap-start"
                           @if($marqueeDuplicate) tabindex="-1" @endif>
                            <div class="relative h-full w-full overflow-hidden rounded-2xl bg-stone-100">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $marqueeDuplicate ? '' : $category->name }}" class="category-showcase-card__image w-full h-full object-cover" loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='flex');">
                                    <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-primary-100 via-white to-amber-50">
                                        <span class="font-display text-5xl font-bold text-stone-400">{{ $category->name[0] ?? 'G' }}</span>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-amber-50">
                                        <span class="font-display text-5xl font-bold text-stone-400 transition-colors duration-300 group-hover:text-primary-500">{{ $category->name[0] ?? 'G' }}</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[#032f18] via-[#032f18]/52 via-30% to-white/10"></div>
                                <div class="absolute inset-x-0 top-0 flex items-start justify-between p-3 sm:p-4">
                                    <span class="inline-flex items-center rounded-full border border-white/35 bg-white/16 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.15em] text-white/90 backdrop-blur-md">
                                        Category
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-white/88 px-2 py-1 text-[9px] font-bold uppercase tracking-[0.1em] text-primary-700 shadow-sm">
                                        GiftSriLanka
                                    </span>
                                </div>
                                <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4 text-center">
                                    <h3 class="font-display text-xl sm:text-2xl lg:text-[1.45rem] font-bold leading-tight tracking-tight text-white drop-shadow-[0_6px_16px_rgba(0,0,0,0.35)]">
                                        {{ $category->name }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Featured Products: product cards with red discount ribbon, green Add to Cart, yellow wishlist --}}
@if($featuredProducts->isNotEmpty())
<section class="relative z-10 py-16 lg:py-20 max-md:py-10 max-md:pt-8 bg-stone-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-4">
        <header class="mb-10 max-md:mb-6 md:mb-12 lg:mb-14">
            <p class="text-sm font-semibold text-primary-600 uppercase tracking-wider mb-2 max-md:text-xs max-md:tracking-wide">Handpicked for you</p>
            <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-800 tracking-tight max-md:text-xl">Featured Products</h2>
            <span class="inline-block mt-2 h-0.5 w-14 rounded-full bg-primary-500 md:mt-3 md:w-16" aria-hidden="true"></span>
        </header>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 max-md:gap-3 md:gap-7">
            @foreach($featuredProducts->take(8) as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-12 text-center max-md:mt-10 md:mt-14">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-primary-500 text-white font-semibold shadow-md shadow-primary-500/20 hover:bg-primary-600 hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 md:px-10 md:py-4 md:rounded-2xl md:hover:-translate-y-0.5">
                View all products
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Trending: latest products — same brightness as Featured (solid bg, no opacity); mobile: horizontal scroll; desktop: grid --}}
@if($trendingProducts->isNotEmpty())
<section id="trending-now" class="home-trending-section relative z-10 py-12 lg:py-16 max-md:py-8 bg-stone-50 border-t border-stone-100" aria-labelledby="trending-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-0">
        {{-- Header: compact on mobile with pill --}}
        <header class="mb-6 max-md:mb-4 max-md:px-5 max-md:flex max-md:items-center max-md:justify-between max-md:gap-3">
            <div class="max-md:min-w-0">
                <div class="max-md:flex max-md:items-center max-md:gap-2 max-md:mb-1">
                    <!-- <span class="max-md:inline-flex max-md:items-center max-md:gap-1 max-md:px-2 max-md:py-0.5 max-md:rounded-full max-md:bg-primary-100 max-md:text-primary-700 max-md:text-[10px] max-md:font-bold max-md:uppercase max-md:tracking-wider">New</span> -->
                    <p class="text-xs sm:text-sm font-semibold text-stone-500 uppercase tracking-[0.2em] mb-2 max-md:mb-0 max-md:text-[11px] max-md:tracking-wide">New arrivals</p>
                </div>
                <h2 id="trending-heading" class="font-display text-3xl font-bold text-stone-800 tracking-tight">Trending now</h2>
                <span class="inline-block mt-2 h-0.5 w-14 rounded-full bg-primary-500 max-md:mt-1.5 max-md:w-10" aria-hidden="true"></span>
            </div>
            <a href="{{ route('shop.index') }}" class="max-md:shrink-0 max-md:inline-flex max-md:items-center max-md:gap-1.5 max-md:px-3.5 max-md:py-2 max-md:rounded-xl max-md:bg-primary-500 max-md:text-white max-md:text-sm max-md:font-semibold max-md:shadow-md max-md:active:scale-[0.98] md:hidden">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </header>
        {{-- Mobile only: horizontal scroll carousel with snap + auto-scroll left --}}
        <div class="md:hidden" x-data="trendingMobileAutoScroll()" x-init="init()">
            <div x-ref="trendingScroll"
                 class="overflow-x-auto overflow-y-hidden scrollbar-hide scroll-smooth snap-x snap-mandatory flex gap-3 pb-3 px-5 -mx-px touch-pan-x"
                 style="-webkit-overflow-scrolling: touch;">
                @foreach($trendingProducts->take(6) as $product)
                    <div class="flex-shrink-0 w-[200px] snap-start">
                        <x-product-card :product="$product" :largeFont="true" />
                    </div>
                @endforeach
            </div>
        </div>
        {{-- Desktop: grid + View all link — trending card UI (desktop only) --}}
        <div class="trending-now-desktop hidden md:grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5 lg:gap-6">
            @foreach($trendingProducts->take(6) as $product)
                <x-product-card :product="$product" :trending="true" />
            @endforeach
        </div>
        <div class="mt-8 text-center max-md:hidden">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-primary-600 font-semibold hover:text-primary-700 hover:bg-primary-50/60 transition-all duration-200">
                View all products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

@php
    $testimonials = [
        ['name' => 'Nadeesha Perera', 'text' => 'Beautiful gifts and super fast delivery! The gift wrapping was perfect for my mother\'s birthday. Highly recommend GIFTSRILANKA.', 'rating' => 5, 'initials' => 'NP'],
        ['name' => 'Rajitha Fernando', 'text' => 'Ordered a hamper for a corporate event. Quality was excellent and the team was very helpful. Will order again.', 'rating' => 5, 'initials' => 'RF'],
        ['name' => 'Chamari Silva', 'text' => 'Love the variety and the secure payment options. My order arrived on time and exactly as described.', 'rating' => 5, 'initials' => 'CS'],
    ];
@endphp
{{-- Customer Testimonials: "What Our Customers Say" — 3 fixed cards --}}
<section id="testimonials" class="home-testimonials-section relative z-10 py-16 lg:py-20 max-md:py-12 bg-[#F9FAFB] overflow-hidden" aria-labelledby="testimonials-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-0">
        <header class="text-center mb-10 lg:mb-12 max-md:mb-6 md:mb-12 max-md:px-5">
            <p class="text-sm font-semibold text-primary-600 uppercase tracking-wider mb-2 max-md:text-xs max-md:tracking-wide">Testimonials</p>
            <h2 id="testimonials-heading" class="font-display text-2xl sm:text-3xl font-bold text-stone-800 tracking-tight max-md:text-xl">
                What Our Customers Say
            </h2>
            <span class="inline-block mt-2 h-0.5 w-14 rounded-full bg-primary-500 mx-auto md:mt-3 md:w-16 max-md:w-10" aria-hidden="true"></span>
        </header>
        {{-- Mobile only: horizontal swipe carousel, quote-style cards --}}
        <div class="md:hidden overflow-x-auto overflow-y-hidden scrollbar-hide scroll-smooth snap-x snap-mandatory touch-pan-x -mx-4 px-4 pb-2" style="-webkit-overflow-scrolling: touch;">
            <div class="flex gap-4 min-w-0">
                @foreach($testimonials as $t)
                    <article class="flex-shrink-0 w-[85vw] max-w-[320px] snap-center snap-always rounded-2xl bg-white p-5 shadow-[0_4px_16px_-4px_rgba(0,0,0,0.08),0_0_1px_rgba(0,0,0,0.06)] border border-stone-100">
                        <div class="flex flex-col h-full">
                            <span class="text-primary-500/40 mb-2" aria-hidden="true">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.998 3.638-3.998 5.849h3.002v10h-9z"/></svg>
                            </span>
                            <p class="text-stone-600 text-sm leading-relaxed flex-1 mb-4">{{ $t['text'] }}</p>
                            <div class="flex items-center gap-3 pt-3 border-t border-stone-100">
                                <div class="shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center text-white font-display font-bold text-xs shadow-sm">
                                    {{ $t['initials'] }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-display font-semibold text-stone-800 text-sm truncate">{{ $t['name'] }}</p>
                                    <div class="flex items-center gap-0.5 mt-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                            <span class="text-[#FBBF24]" aria-hidden="true">
                                                <svg class="w-4 h-4" fill="{{ $s <= $t['rating'] ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            </span>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- Desktop: original 3-column grid (unchanged) --}}
        <div class="hidden md:grid grid-cols-3 gap-6 lg:gap-8 md:gap-7">
            @foreach($testimonials as $t)
                <article class="testimonial-card rounded-2xl bg-white p-6 sm:p-7 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.06),0_6px_20px_-4px_rgba(0,0,0,0.04)] border border-stone-100 transition-all duration-300 md:rounded-[1.25rem] md:shadow-[0_4px_16px_-4px_rgba(0,0,0,0.06),0_8px_24px_-6px_rgba(0,0,0,0.05)] md:border-stone-200/80 md:hover:shadow-[0_12px_32px_-8px_rgba(0,0,0,0.1),0_0_0_1px_rgba(16,185,129,0.2)] md:hover:-translate-y-1.5 md:hover:border-primary-100">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center text-white font-display font-bold text-sm shadow-sm">
                                {{ $t['initials'] }}
                            </div>
                            <div class="flex flex-wrap items-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="text-[#FBBF24]" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="{{ $s <= $t['rating'] ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-stone-600 text-sm sm:text-base leading-relaxed flex-1">{{ $t['text'] }}</p>
                        <p class="mt-4 font-display font-semibold text-stone-800">{{ $t['name'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<!--{{-- Banner / Promotion: full-width, CTAs Green #10B981 hover Yellow #FBBF24; content full brightness --}}-->
<!--<section id="promo-banner" class="home-banner-section relative z-10 py-12 lg:py-16 max-md:py-10 overflow-hidden px-4 sm:px-0 max-md:px-5">-->
<!--    <div class="relative min-h-[280px] sm:min-h-[320px] max-md:min-h-[260px] w-full max-w-7xl mx-auto rounded-2xl overflow-hidden shadow-[0_8px_30px_-8px_rgba(0,0,0,0.12),0_0_1px_rgba(0,0,0,0.06)]">-->
<!--        <div class="absolute inset-0 bg-gradient-to-r from-[#0d4d2a] via-[#166534] to-[#15803d]"></div>-->
<!--        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none" style="background-image: url('{{ asset('images/herobj.webp') }}'); background-size: cover; background-position: center;" aria-hidden="true"></div>-->
<!--        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-[#FBBF24]/20 blur-3xl -translate-y-1/2 translate-x-1/2 z-0 pointer-events-none" aria-hidden="true"></div>-->
<!--        <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full bg-[#10B981]/20 blur-3xl translate-y-1/2 -translate-x-1/2 z-0 pointer-events-none" aria-hidden="true"></div>-->
<!--        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-8 px-6 sm:px-10 lg:px-14 py-12 sm:py-14 min-h-[280px] sm:min-h-[320px] max-md:gap-6 max-md:px-5 max-md:py-10 max-md:min-h-[260px]">-->
<!--            <div class="max-w-xl">-->
<!--                <span class="inline-block px-3 py-1 rounded-full bg-[#FBBF24] text-stone-900 text-xs font-bold uppercase tracking-wider mb-4">Limited time</span>-->
<!--                <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight">-->
<!--                    Festive Sale — Up to <span class="text-[#FBBF24]">30% Off</span> Selected Gifts-->
<!--                </h2>-->
<!--                <p class="mt-3 text-white/85 text-sm sm:text-base">Celebrate every occasion with handpicked gifts. Free delivery on orders over LKR 5,000.</p>-->
<!--                <div class="mt-6 flex flex-wrap gap-3">-->
<!--                    <a href="{{ route('shop.index') }}" class="banner-cta inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-white font-semibold shadow-lg transition-all duration-300 bg-[#10B981] hover:bg-[#FBBF24] hover:text-stone-900">-->
<!--                        Shop the sale-->
<!--                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>-->
<!--                    </a>-->
<!--                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border-2 border-white/60 text-white font-semibold hover:border-[#FBBF24] hover:bg-white/10 transition-all duration-300">-->
<!--                        View all offers-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="hidden md:flex items-center justify-center">-->
<!--                <div class="w-32 h-32 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center border border-white/20">-->
<!--                    <span class="font-display text-4xl font-bold text-[#FBBF24]">30%</span>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

{{-- Why Choose Us: icon cards, white bg, hover lift + accent --}}
<section class="relative z-10 py-16 lg:py-20 max-md:py-12 overflow-hidden" aria-labelledby="why-choose-heading">
    <div class="absolute inset-0 bg-gradient-to-b from-[#F9FAFB] to-stone-50/80 pointer-events-none" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-5">
        <header class="text-center mb-10 lg:mb-12 max-md:mb-8 md:mb-12">
            <p class="text-sm font-semibold text-primary-600 uppercase tracking-wider mb-2">Our promise</p>
            <h2 id="why-choose-heading" class="font-display text-2xl sm:text-3xl font-bold text-stone-800 tracking-tight">
                Why Choose Us
            </h2>
            <span class="inline-block mt-2 h-0.5 w-14 rounded-full bg-primary-500 mx-auto md:mt-3 md:w-16" aria-hidden="true"></span>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-md:gap-5 md:gap-7 lg:gap-8">
            @php
                $reasons = [
                    ['icon' => 'delivery', 'title' => 'Fast Delivery', 'text' => 'Island-wide delivery with tracking. Get your gifts on time, every time.'],
                    ['icon' => 'payment', 'title' => 'Secure Payment', 'text' => 'Safe and encrypted checkout. Multiple payment options for your convenience.'],
                    ['icon' => 'gift', 'title' => 'Gift Wrapping', 'text' => 'Beautiful packaging and personalisation. We make every gift special.'],
                    ['icon' => 'support', 'title' => 'Customer Support', 'text' => 'Dedicated support team ready to help. We\'re here for you before and after your order.'],
                ];
            @endphp
            @foreach($reasons as $r)
                <div class="why-choose-card group rounded-2xl bg-white p-6 sm:p-7 max-md:p-5 border border-stone-100 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.06),0_6px_20px_-4px_rgba(0,0,0,0.04)] transition-all duration-300 md:rounded-[1.25rem] md:border-stone-200/80 md:shadow-[0_4px_16px_-4px_rgba(0,0,0,0.06),0_8px_24px_-6px_rgba(0,0,0,0.05)] md:hover:-translate-y-2 md:hover:shadow-[0_14px_32px_-8px_rgba(0,0,0,0.1),0_0_0_1px_rgba(16,185,129,0.15)] md:hover:border-primary-100">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-4 bg-[#F9FAFB] text-[#10B981] group-hover:bg-[#10B981]/10 group-hover:text-[#10B981] transition-colors duration-300 md:w-16 md:h-16 md:rounded-2xl md:mb-5">
                        @if($r['icon'] === 'delivery')
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        @elseif($r['icon'] === 'payment')
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        @elseif($r['icon'] === 'gift')
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 103 4h12a2 2 0 103-4M5 12V7a2 2 0 012-2h10a2 2 0 012 2v5"/></svg>
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        @endif
                    </div>
                    <h3 class="font-display font-bold text-stone-800 text-lg mb-2">{{ $r['title'] }}</h3>
                    <p class="text-stone-600 text-sm leading-relaxed">{{ $r['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- {{-- Special Offer Banner: green bg, yellow highlight, countdown, red limited badge; full brightness --}}
<section id="free-delivery-offer" class="home-free-delivery-section relative z-10 py-12 lg:py-16 max-md:py-10 bg-primary-600 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 max-md:gap-5">
            <div class="flex items-center gap-4 flex-wrap">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-danger-500 text-white text-sm font-bold uppercase tracking-wide">Limited offer</span>
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-white">
                    <span class="text-warning-300">Free delivery</span> on orders over LKR 5,000
                </h2>
            </div>
            <div x-data="countdown()" class="flex items-center gap-3 sm:gap-4">
                <span class="text-white/80 text-sm font-medium hidden sm:inline">Ends in</span>
                <div class="flex gap-2">
                    <div class="flex flex-col items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/15 backdrop-blur text-white">
                        <span class="text-xl sm:text-2xl font-bold tabular-nums" x-text="days">00</span>
                        <span class="text-xs font-medium text-white/80">Days</span>
                    </div>
                    <div class="flex flex-col items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/15 backdrop-blur text-white">
                        <span class="text-xl sm:text-2xl font-bold tabular-nums" x-text="hours">00</span>
                        <span class="text-xs font-medium text-white/80">Hrs</span>
                    </div>
                    <div class="flex flex-col items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/15 backdrop-blur text-white">
                        <span class="text-xl sm:text-2xl font-bold tabular-nums" x-text="mins">00</span>
                        <span class="text-xs font-medium text-white/80">Min</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', () => {
        const end = new Date();
        end.setDate(end.getDate() + 7);
        return {
            days: '00',
            hours: '00',
            mins: '00',
            init() {
                const tick = () => {
                    const now = new Date();
                    let diff = Math.max(0, end - now);
                    this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
                    diff %= 86400000;
                    this.hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
                    diff %= 3600000;
                    this.mins = String(Math.floor(diff / 60000)).padStart(2, '0');
                };
                tick();
                setInterval(tick, 60000);
            }
        };
    });
});
</script> -->

{{-- CTA strip: "Gift with intention." — full brightness --}}
<section id="gift-with-intention" class="home-gift-cta-section relative z-10 py-16 lg:py-20 max-md:py-12 bg-white border-t border-stone-200">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-5 text-center">
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-stone-800 tracking-tight">Gift with intention.</h2>
        <!-- <p class="mt-3 text-stone-600 text-sm sm:text-base">Free island-wide delivery on selected items. Personalised packaging available.</p> -->
        <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 mt-7 px-8 py-4 rounded-2xl bg-primary-500 text-white font-semibold hover:bg-primary-600 shadow-soft hover:shadow-soft-lg transition-all duration-300">
            Start shopping
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>
</div>
@endsection
