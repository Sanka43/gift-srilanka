<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $guestTitle = ($title ?? 'Welcome') . ' | ' . config('app.name', 'Gift Sri Lanka');
            $guestNoindex = true;
        @endphp
        <title>{{ $guestTitle }}</title>
        <x-seo-head
            :title="$guestTitle"
            :description="config('seo.default_description')"
            :noindex="$guestNoindex"
        />

        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#1F2937] antialiased overflow-x-hidden">
        <div class="min-h-screen min-h-[100vh] flex flex-col lg:flex-row relative isolate z-10">
            {{-- Left: premium brand storytelling (hidden on mobile) --}}
            <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative min-h-screen overflow-hidden flex-col justify-center">
                {{-- Deep luxury gradient: dark emerald to forest green --}}
                <div class="absolute inset-0 bg-gradient-to-br from-[#052e16] via-[#0d4d2a] to-[#14532d]" aria-hidden="true"></div>
                {{-- Subtle blurred gift image in corner (does not overpower text) --}}
                <div class="absolute bottom-0 right-0 w-[85%] h-[75%] bg-cover bg-bottom-right bg-no-repeat opacity-[0.08] blur-[2px]" style="background-image: url('{{ asset('images/loginbg.webp') }}'); background-position: 100% 100%;" aria-hidden="true"></div>
                {{-- Very subtle gold light in one corner --}}
                <div class="absolute top-0 right-0 w-[55%] h-[50%] bg-[radial-gradient(ellipse_at_top_right,_rgba(212,168,75,0.12)_0%,_transparent_60%)]" aria-hidden="true"></div>
                {{-- Soft noise texture overlay for depth --}}
                <div class="absolute inset-0 opacity-[0.04] pointer-events-none" aria-hidden="true" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.85%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3CfeColorMatrix type=%22saturate%22 values=%220%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');"></div>

                <div class="relative z-10 flex flex-col justify-center pl-[10%] pr-[12%] xl:pl-[12%] xl:pr-[15%] py-20 max-w-xl">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm font-medium transition-colors mb-16">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Website
                    </a>

                    <p class="text-[11px] font-medium tracking-[0.35em] text-white/60 uppercase mb-8">GiftSriLanka</p>
                    <h1 class="font-display font-semibold text-white text-[40px] xl:text-[48px] leading-[1.15] tracking-tight">
                        <span class="block whitespace-nowrap">Thoughtful Gifts.</span>
                        <span class="block whitespace-nowrap">Beautiful Moments.</span>
                    </h1>
                    <p class="mt-6 text-stone-300 text-lg leading-relaxed w-full">
                        Discover curated gifts designed to celebrate love, gratitude, and special occasions across Colombo.
                    </p>

                    <ul class="mt-14 space-y-5" aria-label="Features">
                        <li class="flex items-center gap-3 opacity-0 animate-fade-in" style="animation-duration: 0.6s; animation-fill-mode: forwards; animation-delay: 0.15s;">
                            <span class="flex shrink-0 w-8 h-8 rounded-full bg-white/5 border border-amber-400/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </span>
                            <span class="text-sm text-stone-300">Delivery in Colombo</span>
                        </li>
                        <li class="flex items-center gap-3 opacity-0 animate-fade-in" style="animation-duration: 0.6s; animation-fill-mode: forwards; animation-delay: 0.3s;">
                            <span class="flex shrink-0 w-8 h-8 rounded-full bg-white/5 border border-amber-400/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <span class="text-sm text-stone-300">Safe & secure checkout</span>
                        </li>
                        <li class="flex items-center gap-3 opacity-0 animate-fade-in" style="animation-duration: 0.6s; animation-fill-mode: forwards; animation-delay: 0.45s;">
                            <span class="flex shrink-0 w-8 h-8 rounded-full bg-white/5 border border-amber-400/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8 4-8-4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </span>
                            <span class="text-sm text-stone-300">Premium Gift Wrapping</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Right: form section (full width on mobile, then form only + logo) --}}
            <div class="flex-1 min-h-screen flex flex-col items-center lg:justify-center lg:p-10 bg-stone-50">
                {{-- Mobile / tablet: branded hero header --}}
                <div class="lg:hidden relative w-full flex-1 flex flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-[#052e16] via-[#0d4d2a] to-[#14532d] rounded-b-[2rem] px-6 pt-[max(2rem,env(safe-area-inset-top))] pb-20 sm:pb-24 text-center">
                    <img src="{{ asset('images/login-hero.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover object-center" aria-hidden="true" fetchpriority="high">
                    <div class="absolute inset-0 bg-gradient-to-b from-[#052e16]/65 via-[#052e16]/55 to-[#052e16]/85" aria-hidden="true"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_70%_45%_at_50%_45%,_rgba(3,25,12,0.55)_0%,_transparent_100%)]" aria-hidden="true"></div>

                    <a href="{{ route('home') }}" class="relative mx-auto block w-fit" aria-label="Gift Sri Lanka home">
                        <img src="{{ asset('images/logo.png') }}" alt="Gift Sri Lanka" class="h-20 sm:h-24 w-auto drop-shadow-[0_4px_10px_rgba(0,0,0,0.55)] drop-shadow-[0_0_6px_rgba(255,255,255,0.9)]" width="300" height="80">
                    </a>
                    <h1 class="sr-only">{{ $headline ?? 'Welcome' }}</h1>
                </div>

                <div class="w-full max-w-[440px] max-lg:max-w-[480px] max-lg:-mt-10 max-lg:px-4 max-lg:pt-0 max-lg:pb-[max(2rem,env(safe-area-inset-bottom))] max-lg:shrink-0 relative z-10 lg:p-0 min-w-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @include('layouts.partials.snow')
    </body>
</html>
