@php
    $isHeroPage = request()->routeIs('shop.index') || request()->routeIs('home');
    $customOrderWhatsappMessage = rawurlencode('Hi, I want to place a custom order');
    $customOrderWhatsappUrl = 'https://wa.me/94725524212?text=' . $customOrderWhatsappMessage;
@endphp
<div x-data="headerNav()" class="relative z-50" :class="{ 'header-over-hero': isHeroPage && atTop }">
    <div class="fixed top-0 left-0 right-0">
        @if($hasAdvertisementBar ?? false)
            @include('layouts.partials.ad-bar')
        @endif
        <header class="transition-all duration-300 px-3 md:px-4 pt-0">
            <div class="max-w-7xl mx-auto h-14 md:h-16 transition-all duration-300 flex items-center justify-between gap-4 px-4 sm:px-6 md:px-8 nav-bar-inner {{ $isHeroPage ? 'nav-bar-over-hero rounded-t-none rounded-b-xl' : 'nav-bar-solid rounded-xl' }}"
             :class="(isHeroPage && !atTop) ? 'nav-bar-solid rounded-xl' : ''">
            {{-- Left: Logo (icon + wordmark) – white + shadow when over hero for contrast --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 nav-logo">
                <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="Gift Sri Lanka" class="h-8 md:h-9 w-auto object-contain transition-all duration-300 nav-logo-img" width="36" height="36">
                <!-- <span class="font-display font-bold text-stone-900 text-lg md:text-xl tracking-tight transition-colors duration-300 nav-wordmark">Gift Sri Lanka</span> -->
            </a>

            {{-- Center: Nav links – desktop only from md and up --}}
            <nav class="hidden md:flex items-center gap-8 flex-1 justify-center nav-links">
                <a href="{{ route('home') }}" class="text-sm font-medium transition-colors duration-200 nav-link {{ request()->routeIs('home') && !request()->routeIs('shop.index') ? 'text-stone-900 font-semibold' : 'text-stone-500 hover:text-stone-700' }}">Home</a>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium transition-colors duration-200 nav-link {{ request()->routeIs('shop.*') ? 'text-stone-900 font-semibold' : 'text-stone-500 hover:text-stone-700' }}">Shop</a>
            </nav>

            {{-- Right: Cart (red badge), Profile – light gray circular borders; over hero styled via CSS --}}
            <div class="flex items-center gap-2 sm:gap-2.5 nav-icons">
                @auth
                    <a href="{{ $customOrderWhatsappUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="nav-icon max-md:hidden md:flex items-center gap-2 px-3 h-10 rounded-full border-2 border-stone-200 text-stone-600 hover:border-stone-300 hover:text-stone-800 transition-colors duration-200"
                       aria-label="Custom order on WhatsApp">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.05 4.94A9.77 9.77 0 0 0 12.08 2a9.9 9.9 0 0 0-8.54 14.97L2 22l5.2-1.53A9.9 9.9 0 0 0 12.08 22h.01A9.92 9.92 0 0 0 22 12.1a9.75 9.75 0 0 0-2.95-7.16zM12.09 20.3h-.01a8.2 8.2 0 0 1-4.2-1.16l-.3-.18-3.08.9.92-3-.2-.3a8.2 8.2 0 0 1-1.26-4.37 8.23 8.23 0 0 1 8.12-8.3 8.16 8.16 0 0 1 5.81 2.45 8.12 8.12 0 0 1 2.42 5.8 8.24 8.24 0 0 1-8.22 8.16zm4.5-6.16c-.25-.12-1.46-.72-1.68-.8-.23-.08-.39-.12-.56.12-.17.25-.64.8-.79.97-.14.17-.29.2-.54.08-.25-.13-1.05-.39-2-1.24-.74-.66-1.25-1.48-1.39-1.73-.14-.25-.02-.38.1-.5.1-.1.25-.28.37-.41.12-.14.17-.25.25-.42.09-.17.04-.32-.02-.45-.06-.13-.56-1.35-.77-1.85-.2-.49-.4-.42-.56-.43h-.48c-.17 0-.45.06-.69.32-.24.25-.91.9-.91 2.19 0 1.29.94 2.54 1.07 2.72.13.17 1.85 2.83 4.48 3.97.62.27 1.1.42 1.47.54.62.2 1.18.17 1.62.1.5-.07 1.46-.6 1.67-1.17.2-.57.2-1.06.14-1.17-.06-.1-.22-.16-.46-.28z"/>
                        </svg>
                        <span class="text-sm font-semibold leading-none">Customize package</span>
                    </a>
                @endauth
                @auth
                <div class="relative" x-data="{ cartOpen: false }" @click.outside="cartOpen = false">
                    {{-- Mobile: link to cart page --}}
                    <a href="{{ route('cart.index') }}" data-cart-trigger aria-label="Cart" class="nav-icon max-md:flex md:hidden items-center justify-center w-10 h-10 rounded-full border-2 border-stone-200 text-stone-600 hover:border-stone-300 hover:text-stone-800 transition-colors duration-200 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                    </a>
                    {{-- Desktop: button opens dropdown --}}
                    <button type="button" @click="cartOpen = !cartOpen" data-cart-trigger aria-label="Cart"
                            class="nav-icon max-md:hidden md:flex items-center justify-center w-10 h-10 rounded-full border-2 border-stone-200 text-stone-600 hover:border-stone-300 hover:text-stone-800 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                    </button>
                    <div x-show="cartOpen" x-cloak
                         class="absolute top-full right-0 mt-2 w-72 md:w-80 rounded-2xl bg-white border border-stone-200 shadow-xl overflow-hidden z-[100] max-md:left-2 max-md:right-2 max-md:-translate-x-0 max-md:top-auto max-md:bottom-full max-md:mt-0 max-md:mb-2 max-md:w-auto max-md:max-h-[85vh] max-md:flex max-md:flex-col"
                         style="display: none;">
                        <div class="p-4 border-b border-stone-100">
                            <p class="font-display font-semibold text-stone-800">Cart ({{ $cartCount }})</p>
                        </div>
                        @if($cartCount > 0)
                            <ul class="max-h-56 overflow-y-auto max-md:flex-1 max-md:min-h-0 max-md:max-h-none">
                                @foreach($cartPreviewItems as $item)
                                    <li class="p-3 flex gap-3 border-b border-stone-50 hover:bg-stone-50/80">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-stone-100 shrink-0">
                                            @if($item['product']->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item['product']->images->first()->image_path) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-2" aria-hidden="true">
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('products.show', $item['product']->slug) }}" class="font-medium text-stone-800 text-sm line-clamp-2 hover:text-primary-600">{{ $item['product']->name }}</a>
                                            <p class="text-xs text-stone-500">× {{ $item['quantity'] }} · LKR {{ number_format($item['subtotal'], 0) }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="p-4 bg-stone-50 flex justify-between items-center">
                                <span class="font-semibold text-stone-700">Total</span>
                                <span class="text-primary-600 font-bold">LKR {{ number_format($cartTotal, 0) }}</span>
                            </div>
                            <div class="p-3 flex gap-2 max-md:gap-3">
                                <a href="{{ route('cart.index') }}" class="flex-1 text-center py-2.5 max-md:py-3 rounded-xl border border-stone-200 text-sm font-medium text-stone-700 hover:bg-stone-50">View cart</a>
                                <a href="{{ route('checkout.index') }}" class="flex-1 text-center py-2.5 max-md:py-3 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600">Checkout</a>
                            </div>
                        @else
                            <div class="p-6 text-center text-stone-500 text-sm">Your cart is empty.</div>
                            <a href="{{ route('shop.index') }}" class="block p-3 text-center text-primary-600 font-semibold text-sm hover:bg-primary-50/50">Shop now</a>
                        @endif
                    </div>
                </div>
                @endauth

                @auth
                    <a href="{{ route('profile.edit') }}" class="nav-icon hidden md:flex items-center justify-center w-10 h-10 rounded-full border-2 border-stone-200 text-stone-600 hover:border-stone-300 hover:text-stone-800 transition-colors duration-200 overflow-hidden bg-transparent" aria-label="Profile">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . str_replace('\\', '/', auth()->user()->avatar_path)) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="nav-profile-initial text-sm font-semibold text-stone-600">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        @endif
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold bg-primary-500 text-white hover:bg-primary-600 border-2 border-primary-500 hover:border-primary-600 shadow-sm">Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="nav-signin-btn hidden md:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 shadow-sm hover:shadow transition-all duration-200 border-2 border-primary-500 hover:border-primary-600" aria-label="Sign in">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Sign in
                    </a>
                @endauth

                <button @click="menuOpen = true" type="button" class="nav-icon md:hidden flex items-center justify-center w-10 h-10 rounded-full border-2 border-stone-200 text-stone-600 hover:border-stone-300 hover:text-stone-800 transition-colors duration-200" aria-label="Open menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        </header>
    </div>

    {{-- Mobile menu: full-height drawer with slide transition --}}
    <div x-show="menuOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 md:hidden" style="display: none;">
        <div class="absolute inset-0 bg-stone-900/50 backdrop-blur-sm" @click="menuOpen = false" aria-hidden="true"></div>
        <div class="absolute top-0 right-0 bottom-0 w-full max-w-[min(20rem,100%)] bg-white flex flex-col overflow-hidden shadow-luxury-xl"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            {{-- Header strip with primary tint --}}
            <div class="flex items-center justify-between gap-3 px-5 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-stone-100 shrink-0">
                <a href="{{ route('home') }}" @click="menuOpen = false" class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="Gift Sri Lanka" class="h-9 w-auto object-contain shrink-0" width="36" height="36">
                    <span class="font-display font-bold text-stone-800 text-lg truncate">Gift Sri Lanka</span>
                </a>
                <button @click="menuOpen = false" class="flex items-center justify-center w-10 h-10 rounded-full bg-stone-100 text-stone-600 hover:bg-stone-200 hover:text-stone-800 transition-colors" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Search --}}
            <form action="{{ route('shop.index') }}" method="GET" class="px-4 pt-4 pb-3 shrink-0">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="search" name="search" placeholder="Search gifts..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-4 py-3 text-sm text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-primary-400/30 focus:border-primary-400 focus:bg-white transition-colors">
                </div>
            </form>
            {{-- Scrollable nav --}}
            <nav class="flex-1 overflow-y-auto overscroll-contain px-4 pb-6">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('home') }}" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-stone-700 font-semibold hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors {{ request()->routeIs('home') && !request()->routeIs('shop.index') ? 'bg-primary-50 text-primary-700' : '' }}">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-stone-100 text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11v2a1 1 0 01-1 1h2m-6 4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-stone-700 font-semibold hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors {{ request()->routeIs('shop.*') ? 'bg-primary-50 text-primary-700' : '' }}">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-stone-100 text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></span>
                            Shop
                        </a>
                    </li>
                    @auth
                    <li>
                        <a href="{{ $customOrderWhatsappUrl }}" target="_blank" rel="noopener noreferrer" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-stone-700 font-semibold hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-stone-100 text-stone-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19.05 4.94A9.77 9.77 0 0 0 12.08 2a9.9 9.9 0 0 0-8.54 14.97L2 22l5.2-1.53A9.9 9.9 0 0 0 12.08 22h.01A9.92 9.92 0 0 0 22 12.1a9.75 9.75 0 0 0-2.95-7.16zM12.09 20.3h-.01a8.2 8.2 0 0 1-4.2-1.16l-.3-.18-3.08.9.92-3-.2-.3a8.2 8.2 0 0 1-1.26-4.37 8.23 8.23 0 0 1 8.12-8.3 8.16 8.16 0 0 1 5.81 2.45 8.12 8.12 0 0 1 2.42 5.8 8.24 8.24 0 0 1-8.22 8.16zm4.5-6.16c-.25-.12-1.46-.72-1.68-.8-.23-.08-.39-.12-.56.12-.17.25-.64.8-.79.97-.14.17-.29.2-.54.08-.25-.13-1.05-.39-2-1.24-.74-.66-1.25-1.48-1.39-1.73-.14-.25-.02-.38.1-.5.1-.1.25-.28.37-.41.12-.14.17-.25.25-.42.09-.17.04-.32-.02-.45-.06-.13-.56-1.35-.77-1.85-.2-.49-.4-.42-.56-.43h-.48c-.17 0-.45.06-.69.32-.24.25-.91.9-.91 2.19 0 1.29.94 2.54 1.07 2.72.13.17 1.85 2.83 4.48 3.97.62.27 1.1.42 1.47.54.62.2 1.18.17 1.62.1.5-.07 1.46-.6 1.67-1.17.2-.57.2-1.06.14-1.17-.06-.1-.22-.16-.46-.28z"/>
                                </svg>
                            </span>
                            Custom Order
                        </a>
                    </li>
                    @endauth
                </ul>
                <p class="pt-5 pb-2 px-4 text-[11px] font-bold text-stone-400 uppercase tracking-widest">Categories</p>
                <ul class="space-y-0.5">
                    @foreach($navCategories as $cat)
                        <li>
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" @click="menuOpen = false" class="flex items-center gap-3 py-2.5 px-4 rounded-xl text-stone-600 font-medium hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 shrink-0"></span>
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                {{-- Account & actions --}}
                <div class="mt-6 pt-4 border-t border-stone-200 space-y-1">
                    <a href="{{ route('cart.index') }}" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-stone-700 font-semibold hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors">
                        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-stone-100 text-stone-600 relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            @if($cartCount > 0)<span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
                        </span>
                        Cart @if($cartCount > 0)<span class="text-stone-400 font-normal">({{ $cartCount }})</span>@endif
                    </a>
                    @auth
                        <a href="{{ route('profile.edit') }}" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-stone-700 font-semibold hover:bg-primary-50 hover:text-primary-700 active:bg-primary-100/50 transition-colors {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary-700' : '' }}">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary-100 text-primary-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                            Profile
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" @click="menuOpen = false" class="flex items-center gap-3 py-3.5 px-4 rounded-xl font-semibold bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 transition-colors">
                                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-white/20"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                                Admin
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" @click="menuOpen = false" class="flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl font-semibold bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Sign in
                        </a>
                    @endauth
                </div>
            </nav>
        </div>
    </div>

    <div class="{{ $isHeroPage ? 'h-0' : (($hasAdvertisementBar ?? false) ? 'h-[6.25rem] md:h-[6.5rem]' : 'h-[4.25rem] md:h-[5rem]') }}" aria-hidden="true"></div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('headerNav', () => ({
        menuOpen: false,
        isHeroPage: document.body.classList.contains('page-shop') || document.body.classList.contains('page-home'),
        atTop: true,
        init() {
            if (!this.isHeroPage) return;
            const check = () => { this.atTop = window.scrollY < 120; };
            window.addEventListener('scroll', check, { passive: true });
            check();
        }
    }));
});
</script>
