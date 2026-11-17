@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-20 text-center" x-data="{ mounted: false }" x-init="$nextTick(() => mounted = true)">
    {{-- Big 404 with gradient and depth --}}
    <div class="relative inline-block mb-6">
        <span class="font-display text-[10rem] sm:text-[12rem] font-bold leading-none select-none block bg-gradient-to-br from-primary-400 to-primary-600 bg-clip-text text-transparent drop-shadow-[0_0_40px_rgba(34,197,94,0.25)]"
              :class="mounted ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
              style="transition: opacity 0.5s ease, transform 0.5s ease;">404</span>
        <span class="absolute inset-0 font-display text-[14rem] sm:text-[18rem] font-bold leading-none text-stone-300/70 -z-10 blur-md select-none"
              :class="mounted ? 'opacity-100' : 'opacity-0'"
              style="transition: opacity 0.6s ease 0.1s;">404</span>
    </div>

    <h1 class="font-display text-2xl sm:text-3xl font-bold text-stone-800 mb-3 tracking-tight"
        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
        style="transition: opacity 0.5s ease 0.15s, transform 0.5s ease 0.15s;">Page not found</h1>
    <p class="text-stone-600 text-lg max-w-md mx-auto mb-10"
       :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
       style="transition: opacity 0.5s ease 0.25s, transform 0.5s ease 0.25s;">The page you're looking for doesn't exist or has been moved.</p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
         style="transition: opacity 0.5s ease 0.35s, transform 0.5s ease 0.35s;">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-primary-600 text-white rounded-2xl font-semibold text-base shadow-lg shadow-primary-500/25 hover:bg-primary-700 hover:shadow-primary-500/30 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Back to home
        </a>
        <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 border-2 border-stone-300 text-stone-700 rounded-2xl font-semibold text-base hover:border-primary-400 hover:text-primary-700 hover:bg-primary-50 transition-all duration-200">
            Browse shop
        </a>
    </div>
</div>
@endsection
