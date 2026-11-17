<x-guest-layout>
    <x-slot name="title">Login</x-slot>
    <x-slot name="headline">Welcome Back</x-slot>
    <x-slot name="subtitle">Sign in to your account to continue your journey with Gift Sri Lanka.</x-slot>

    <div class="rounded-2xl bg-white shadow-xl shadow-stone-200/60 border border-stone-200/80 p-8 sm:p-10 max-md:p-5 max-md:rounded-xl opacity-0 animate-fade-in-up" style="animation-duration: 0.5s; animation-fill-mode: forwards;" x-data="{ showPassword: false }">
        {{-- Header --}}
        <div class="text-center sm:text-left">
            <h2 class="font-display text-2xl max-md:text-xl font-bold text-stone-800 tracking-tight">Sign in</h2>
            <p class="mt-1 text-sm text-stone-500">Enter your details to access your account.</p>
        </div>

        <x-auth-session-status class="mt-5 text-sm text-emerald-700 rounded-lg bg-emerald-50 px-3 py-2" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-stone-700 mb-1.5">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full h-12 max-md:min-h-[2.75rem] pl-10 pr-4 rounded-xl border border-stone-300 bg-stone-50/50 text-stone-800 placeholder-stone-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-stone-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition">Forgot?</a>
                    @endif
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                           class="w-full h-12 max-md:min-h-[2.75rem] pl-10 pr-11 rounded-xl border border-stone-300 bg-stone-50/50 text-stone-800 placeholder-stone-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-600 transition" aria-label="Toggle password visibility">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500/30 size-4">
                <span class="text-sm text-stone-600">Remember me</span>
            </label>

            <button type="submit" class="w-full h-12 max-md:min-h-[2.75rem] max-md:py-3 rounded-xl bg-stone-800 text-white text-sm font-semibold hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 transition">
                Sign in
            </button>
        </form>

        @if (Route::has('register'))
            <div class="relative mt-8" aria-hidden="true">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-stone-200"></div>
                </div>
                <!-- <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs font-medium text-stone-400 uppercase tracking-wider">Or continue with</span>
                </div> -->
            </div>
            <!-- <a href="#" class="mt-6 flex items-center justify-center gap-2 w-full h-11 max-md:min-h-[2.75rem] max-md:py-3 rounded-xl border border-stone-300 bg-white text-stone-700 font-medium text-sm hover:bg-stone-50 transition" aria-label="Continue with Google (not configured)">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </a> -->
            <p class="mt-6 text-center text-sm text-stone-500">
                No account? <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition">Create one</a>
            </p>
        @endif
    </div>
</x-guest-layout>
