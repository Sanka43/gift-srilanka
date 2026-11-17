@extends('layouts.app')

@section('title', isset($seoCategory) && $seoCategory ? $seoCategory->name . ' – Shop' : 'Shop')
@section('meta_description', isset($seoCategory) && $seoCategory ? 'Shop ' . $seoCategory->name . ' – ' . config('seo.site_name') . '. ' . config('seo.default_description') : config('seo.default_description'))
@section('canonical', url()->current())
@section('fullBleed', '1')

@includeWhen(isset($seoCategory) && $seoCategory, 'shop.partials.structured-data-breadcrumb')

@php
    $minPrice = (int) ($priceRange->min ?? 0);
    $maxPrice = (int) ($priceRange->max ?? 100000);
    $currentMin = request('min_price', '');
    $currentMax = request('max_price', '');
    $currentCategory = request('category', '');
    $currentOccasions = request('occasion', []);
    $currentOccasions = is_array($currentOccasions) ? $currentOccasions : [];
    $currentRecipients = request('recipient', []);
    $currentRecipients = is_array($currentRecipients) ? $currentRecipients : [];
    $occasions = ['Birthday', 'Anniversary', 'Valentine', 'Wedding', 'New Baby', 'Corporate'];
    $recipients = ['For Him', 'For Her', 'For Kids', 'For Parents', 'For Friends'];
    $deliveryOptions = ['one_to_three_days' => '1-3 days', 'three_to_five_days' => '3-5 days'];
    $hasActiveFilters = request()->hasAny(['category', 'search', 'min_price', 'max_price', 'sort', 'occasion', 'recipient', 'delivery_type', 'rating', 'in_stock', 'on_sale']);
    $categoryDisplayNames = ['' => 'All'];
    foreach ($categories ?? [] as $cat) {
        $categoryDisplayNames[$cat->slug] = $cat->name;
    }
@endphp

@section('content')
{{-- Hero: compact height and smaller elements --}}
<section class="relative min-h-[28vh] md:min-h-[32vh] max-md:min-h-[24vh] flex flex-col items-center justify-center pt-20 md:pt-24 px-4 pb-12 md:pb-16 max-md:pt-14 max-md:px-3 max-md:pb-10 bg-stone-800 overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/shopbg.jpg') }}" alt="Gift Sri Lanka shop – flowers, cake, gifts" class="w-full h-full object-cover opacity-90" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/30" aria-hidden="true"></div>
    </div>
    <div class="relative z-10 text-center max-w-xl mx-auto w-full max-md:px-0 max-md:w-full">
        <h1 class="text-3xl md:text-4xl lg:text-5xl max-md:text-2xl font-display font-bold text-white/95 tracking-tight drop-shadow-lg">Shop</h1>
        <p class="mt-1.5 text-base md:text-lg max-md:mt-1 max-md:text-sm font-semibold text-white/90 drop-shadow-md">Give All You Need</p>
        <form action="{{ route('shop.index') }}" method="GET" class="mt-4 flex flex-col sm:flex-row gap-2 max-w-lg mx-auto max-md:mt-3 max-md:gap-2 max-md:max-w-none">
            <label for="hero-search" class="sr-only">Search</label>
            <input type="search" id="hero-search" name="search" value="{{ request('search') }}" placeholder="Search gifts..."
                   class="flex-1 min-w-0 rounded-lg border-0 bg-stone-100/95 py-2.5 pl-4 pr-3 text-sm max-md:py-2.5 max-md:min-h-[40px] max-md:rounded-xl max-md:pl-3 max-md:text-sm text-stone-800 placeholder-stone-500 focus:ring-2 focus:ring-primary-400 shadow-lg">
            <button type="submit" class="rounded-lg bg-stone-900 text-white font-semibold px-4 py-2.5 text-sm max-md:py-2.5 max-md:min-h-[40px] max-md:rounded-xl max-md:w-full shadow-lg hover:bg-stone-800 transition-colors whitespace-nowrap">Search</button>
        </form>
    </div>
</section>

<div class="min-h-screen bg-stone-50" x-data="shopPage({{ $minPrice }}, {{ $maxPrice }}, {{ json_encode($currentMin) }}, {{ json_encode($currentMax) }}, {{ json_encode($currentCategory) }}, {{ json_encode(request('sort', 'latest')) }}, {{ json_encode($currentOccasions) }}, {{ json_encode($currentRecipients) }}, {{ json_encode(request('delivery_type', '')) }}, {{ json_encode(request('rating', '')) }}, {{ request('in_stock') ? 'true' : 'false' }}, {{ request('on_sale') ? 'true' : 'false' }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10 max-md:px-3 max-md:py-5">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
            {{-- Filter sidebar (desktop) --}}
            <aside class="hidden lg:block lg:w-56 xl:w-64 flex-shrink-0">
                <div class="shop-filter-card rounded-xl bg-white border border-stone-200/80 shadow-sm overflow-hidden sticky top-24">
                    <div class="px-4 py-3 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-stone-800">Filters</h2>
                        <a href="{{ route('shop.index') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="text-xs text-stone-500 hover:text-primary-600">Clear</a>
                    </div>
                    <div class="p-4 max-h-[calc(100vh-10rem)] overflow-y-auto">
                        @include('shop.partials.filter-form', ['isMobile' => false, 'submitAction' => 'autoSubmit', 'submitActionDebounced' => 'applyFiltersDebounced', 'formId' => 'shop-filters', 'formRef' => 'filterForm'])
                    </div>
                    <div class="px-4 py-3 border-t border-stone-100 bg-stone-50/50">
                        <a href="{{ route('shop.index') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="flex items-center justify-center gap-1.5 w-full py-2 rounded-lg text-stone-600 text-xs font-medium hover:bg-stone-100 hover:text-stone-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear all
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main: products area --}}
            <div class="flex-1 min-w-0 lg:pb-0">
                {{-- Mobile: Filter button at top of products --}}
                <div class="lg:hidden mb-4">
                    <button type="button" @click="filterDrawerOpen = true" class="w-full inline-flex items-center justify-center gap-2.5 min-h-[48px] py-3 rounded-2xl shadow-soft border border-stone-200/80 bg-white font-semibold text-base text-stone-700 hover:bg-stone-50 transition-all duration-300 active:scale-[0.98]">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                        @if($hasActiveFilters)
                            <span class="bg-primary-500 text-white text-xs font-semibold rounded-full min-w-[1.25rem] h-5 px-1.5 flex items-center justify-center">{{ count($currentOccasions) + count($currentRecipients) + (request('delivery_type') ? 1 : 0) + (request('rating') ? 1 : 0) + (request('in_stock') ? 1 : 0) + (request('on_sale') ? 1 : 0) + (request('category') ? 1 : 0) + (request('min_price') || request('max_price') ? 1 : 0) }}</span>
                        @endif
                    </button>
                </div>

                {{-- Active filter tags: premium removable chips — mobile: horizontal scroll strip --}}
                @if($hasActiveFilters)
                <div class="mb-6 flex flex-wrap items-center gap-2 max-md:mb-4 max-md:gap-2 max-md:flex-nowrap max-md:overflow-x-auto max-md:-mx-3 max-md:px-3 max-md:pb-1 max-md:scroll-smooth scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-400 mr-1 max-md:flex-shrink-0 max-md:mr-2">Filters</span>
                    @if(request('category'))
                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">{{ $categoryDisplayNames[request('category')] ?? request('category') }} <span class="text-primary-500" aria-hidden="true">&times;</span></a>
                    @endif
                    @foreach($currentOccasions as $o)
                        <a href="{{ request()->fullUrlWithQuery(['occasion' => array_values(array_diff($currentOccasions, [$o])), 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">{{ $o }} <span class="text-primary-500">&times;</span></a>
                    @endforeach
                    @foreach($currentRecipients as $r)
                        <a href="{{ request()->fullUrlWithQuery(['recipient' => array_values(array_diff($currentRecipients, [$r])), 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">{{ $r }} <span class="text-primary-500">&times;</span></a>
                    @endforeach
                    @if(request('delivery_type'))
                        <a href="{{ request()->fullUrlWithQuery(['delivery_type' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">{{ $deliveryOptions[request('delivery_type')] ?? request('delivery_type') }} <span class="text-primary-500">&times;</span></a>
                    @endif
                    @if(request('rating'))
                        <a href="{{ request()->fullUrlWithQuery(['rating' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">{{ request('rating') }}★+ <span class="text-primary-500">&times;</span></a>
                    @endif
                    @if(request('in_stock'))
                        <a href="{{ request()->fullUrlWithQuery(['in_stock' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">In Stock <span class="text-primary-500">&times;</span></a>
                    @endif
                    @if(request('on_sale'))
                        <a href="{{ request()->fullUrlWithQuery(['on_sale' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">Sale <span class="text-primary-500">&times;</span></a>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition-all duration-200 shadow-sm max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl">Price <span class="text-primary-500">&times;</span></a>
                    @endif
                    <a href="{{ route('shop.index') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-all duration-200 max-md:flex-shrink-0 max-md:py-2.5 max-md:rounded-2xl max-md:font-semibold">Clear all</a>
                </div>
                @endif

                {{-- Top bar: results count + sort — mobile: compact row, full-width sort --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 max-md:flex-row max-md:items-center max-md:justify-between max-md:gap-3 max-md:mb-5">
                    <p class="text-sm text-stone-600 max-md:text-xs max-md:font-medium max-md:text-stone-500">
                        @if($products->total() > 0)
                            <span class="font-semibold text-stone-800 max-md:font-bold max-md:text-stone-700">{{ $products->total() }}</span> {{ $products->total() === 1 ? 'Product' : 'Products' }}
                        @else
                            No products found
                        @endif
                    </p>
                    <form method="GET" action="{{ route('shop.index') }}" id="shop-sort-form" class="flex items-center gap-2 max-md:flex-1 max-md:min-w-0 max-md:justify-end">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        @foreach($currentOccasions as $o)<input type="hidden" name="occasion[]" value="{{ $o }}">@endforeach
                        @foreach($currentRecipients as $r)<input type="hidden" name="recipient[]" value="{{ $r }}">@endforeach
                        <input type="hidden" name="delivery_type" value="{{ request('delivery_type') }}">
                        <input type="hidden" name="rating" value="{{ request('rating') }}">
                        @if(request('in_stock'))<input type="hidden" name="in_stock" value="1">@endif
                        @if(request('on_sale'))<input type="hidden" name="on_sale" value="1">@endif
                        <label for="shop-sort" class="text-sm font-medium text-stone-600 whitespace-nowrap max-md:text-xs max-md:font-semibold max-md:text-stone-600">Sort</label>
                        <select id="shop-sort" name="sort" onchange="this.form.submit()"
                                class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm text-stone-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-300 max-md:flex-1 max-md:min-w-0 max-md:max-w-[180px] max-md:py-2.5 max-md:rounded-2xl max-md:text-sm max-md:border-stone-200">
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Best Rated</option>
                        </select>
                    </form>
                </div>

                @if(request('search'))
                    <p class="mb-4 text-sm text-stone-600">
                        Results for <strong class="text-stone-800">"{{ request('search') }}"</strong>
                    </p>
                @endif

                {{-- Product grid — mobile: 2 cols, comfortable gap and bottom padding --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 max-md:gap-3 max-md:pb-2">
                    @forelse($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        {{-- Empty state --}}
                        <div class="col-span-full flex flex-col items-center justify-center py-16 px-6 rounded-2xl bg-white border border-stone-200 shadow-soft">
                            <div class="w-24 h-24 mb-6 text-stone-200">
                                <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path d="M60 20L75 45H45L60 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M45 45H75L70 85H50L45 45Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <rect x="35" y="85" width="50" height="8" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M52 55V70M60 55V70M68 55V70" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-display font-semibold text-stone-800">Exciting Products Are On the Way</h3>
                            <p class="mt-2 text-sm text-stone-500 text-center max-w-sm">We are currently adding new items to this category. Check back soon to explore our latest collection.</p>
                            <a href="{{ route('shop.index') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="mt-6 inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition-all duration-300">
                                Clear Filters
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination — mobile: more padding and touch-friendly --}}
                @if($products->hasPages())
                    <div class="mt-10 pt-8 border-t border-stone-200 flex justify-center max-md:mt-8 max-md:pt-6 max-md:pb-4">
                        {{ $products->withQueryString()->links('pagination.shop') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Recommendations carousel — mobile: larger tap targets, snap scroll --}}
        @if(isset($recommendations) && $recommendations->isNotEmpty())
        <section class="mt-16 pt-12 border-t border-stone-200 max-md:mt-10 max-md:pt-8 max-md:border-t-stone-200" x-data="{ scrollEl: null }" x-ref="recsSection">
            <div class="flex items-center justify-between gap-4 mb-6 max-md:mb-4 max-md:px-0">
                <h2 class="font-display font-bold text-stone-900 text-2xl md:text-3xl max-md:text-lg max-md:font-extrabold">Explore our recommendations</h2>
                <div class="flex items-center gap-2 max-md:gap-1.5">
                    <button type="button" @click="$refs.recsScroll.scrollBy({ left: -320, behavior: 'smooth' })" class="p-2.5 rounded-full border border-stone-200 bg-white text-stone-600 hover:bg-stone-50 hover:text-stone-800 transition-colors max-md:min-h-[44px] max-md:min-w-[44px] max-md:flex max-md:items-center max-md:justify-center max-md:p-0 max-md:rounded-2xl" aria-label="Previous">
                        <svg class="w-5 h-5 max-md:w-5 max-md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" @click="$refs.recsScroll.scrollBy({ left: 320, behavior: 'smooth' })" class="p-2.5 rounded-full border border-stone-200 bg-white text-stone-600 hover:bg-stone-50 hover:text-stone-800 transition-colors max-md:min-h-[44px] max-md:min-w-[44px] max-md:flex max-md:items-center max-md:justify-center max-md:p-0 max-md:rounded-2xl" aria-label="Next">
                        <svg class="w-5 h-5 max-md:w-5 max-md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
            <div x-ref="recsScroll" class="flex gap-6 overflow-x-auto pb-4 -mx-4 px-4 scroll-smooth max-md:gap-4 max-md:-mx-3 max-md:px-3 max-md:pb-6 max-md:snap-x max-md:snap-mandatory" style="scrollbar-width: thin;">
                @foreach($recommendations as $product)
                    <div class="flex-shrink-0 w-[280px] sm:w-[300px] max-md:w-[260px] max-md:snap-start max-md:snap-always">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>

    {{-- Mobile filter drawer: slides in from left — mobile UI refinements --}}
    <div x-show="filterDrawerOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 lg:hidden" aria-modal="true">
        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm max-md:bg-stone-900/50" @click="filterDrawerOpen = false"></div>
        <div class="fixed inset-y-0 left-0 w-full max-w-[300px] max-md:max-w-[min(100%,20rem)] bg-white shadow-xl overflow-hidden flex flex-col max-md:rounded-r-2xl max-md:shadow-2xl"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            <div class="flex-none px-4 py-3 border-b border-stone-200 flex items-center justify-between max-md:px-4 max-md:py-4 max-md:border-b-stone-200">
                <h2 class="text-sm font-semibold text-stone-800 max-md:text-base max-md:font-bold">Filters</h2>
                <button type="button" @click="filterDrawerOpen = false" class="p-2 -m-2 rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-700 max-md:min-h-[44px] max-md:min-w-[44px] max-md:flex max-md:items-center max-md:justify-center max-md:rounded-xl" aria-label="Close">
                    <svg class="w-5 h-5 max-md:w-6 max-md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 max-md:p-4 max-md:overscroll-contain">
                @include('shop.partials.filter-form', ['isMobile' => true, 'submitAction' => 'submitMobile', 'submitActionDebounced' => 'applyFiltersDebouncedMobile', 'formId' => 'shop-filters-mobile', 'formRef' => 'filterFormMobile'])
            </div>
            <div class="flex-none p-4 border-t border-stone-200 flex gap-2 max-md:p-4 max-md:gap-3 max-md:bg-stone-50/80 max-md:border-t-stone-200">
                <a href="{{ route('shop.index') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-lg border border-stone-200 text-stone-600 text-sm font-medium hover:bg-stone-50 max-md:py-3.5 max-md:rounded-2xl max-md:font-semibold max-md:min-h-[48px]" @click="filterDrawerOpen = false">Clear</a>
                <button type="button" @click="submitMobile()" class="flex-1 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition-colors max-md:py-3.5 max-md:rounded-2xl max-md:font-bold max-md:min-h-[48px] max-md:shadow-soft-lg">Apply</button>
            </div>
        </div>
    </div>
</div>
@endsection
