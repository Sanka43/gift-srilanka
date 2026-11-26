<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $pageTitle = trim((string) view()->yieldContent('title'));
            if ($pageTitle === '') {
                $pageTitle = 'Shop';
            }
            $fullTitle = $pageTitle . ' | ' . config('app.name', 'Gifts Sri Lanka');
            $metaDescription = trim((string) view()->yieldContent('meta_description'));
            $metaImage = trim((string) view()->yieldContent('meta_image'));
            $canonicalUrl = trim((string) view()->yieldContent('canonical'));
            $noindex = (bool) view()->yieldContent('noindex');
            $ogType = trim((string) view()->yieldContent('og_type')) ?: 'website';
        @endphp
        <title>{{ $fullTitle }}</title>
        <x-seo-head
            :title="$fullTitle"
            :description="$metaDescription ?: null"
            :image="$metaImage ?: null"
            :canonical="$canonicalUrl ?: null"
            :noindex="$noindex"
            :type="$ogType"
        />

        @include('layouts.partials.structured-data-default')

        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts: Outfit + DM Sans are self-hosted via Vite (see resources/css/app.css) -->

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @if(config('contact.google_analytics_id'))
        <!-- Google tag (gtag.js) - loaded after the page is interactive so it never blocks rendering -->
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ config('contact.google_analytics_id') }}');

            window.addEventListener('load', function () {
                var loadGa = function () {
                    var s = document.createElement('script');
                    s.async = true;
                    s.src = 'https://www.googletagmanager.com/gtag/js?id={{ config('contact.google_analytics_id') }}';
                    document.head.appendChild(s);
                };
                if ('requestIdleCallback' in window) { requestIdleCallback(loadGa, { timeout: 3000 }); } else { setTimeout(loadGa, 1500); }
            });
        </script>
        @endif
    </head>
    <body class="font-sans antialiased bg-stone-50 overflow-x-hidden @if(request()->routeIs('shop.index')) page-shop @endif @if(request()->routeIs('home')) page-home @endif">
        <div class="min-h-screen flex flex-col relative isolate z-10">
            @include('layouts.partials.navbar')

            <!-- Flash messages (global, auto-dismiss ~5s) -->
            @if(session('success') || session('error') || session('info'))
                <div x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 5000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 scale-95" class="fixed top-20 max-md:top-[4.5rem] max-md:left-4 max-md:right-4 max-md:translate-x-0 max-md:mx-0 left-1/2 -translate-x-1/2 z-[60] max-w-md max-md:max-w-none w-auto mx-auto px-4">
                    @if(session('success'))
                        <div class="rounded-2xl bg-primary-50/95 backdrop-blur border border-primary-200 text-primary-800 px-5 py-3 text-sm font-medium shadow-soft-lg animate-fade-in">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="rounded-2xl bg-danger-50/95 backdrop-blur border border-danger-200 text-danger-800 px-5 py-3 text-sm font-medium shadow-soft-lg animate-fade-in">{{ session('error') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="rounded-2xl bg-warning-50/95 backdrop-blur border border-warning-200 text-warning-800 px-5 py-3 text-sm font-medium shadow-soft-lg animate-fade-in">{{ session('info') }}</div>
                    @endif
                </div>
            @endif

            {{-- Page Content: full-bleed when yield('fullBleed') is true; also supports component slot. --}}
            @hasSection('fullBleed')
            <main class="flex-1">
                @yield('content')
                @isset($slot){{ $slot }}@endisset
            </main>
            @else
            <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
                @isset($slot){{ $slot }}@endisset
            </main>
            @endif
            @include('layouts.partials.footer')
        </div>
        @if(request()->routeIs('home') || request()->routeIs('shop.index'))
            @include('layouts.partials.customer-chat')
        @endif
        @include('layouts.partials.snow')
        @stack('structured-data')
    </body>
</html>
