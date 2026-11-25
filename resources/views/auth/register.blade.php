<x-guest-layout>
    <x-slot name="title">Sign up</x-slot>
    <x-slot name="headline">Create Your Account</x-slot>
    <x-slot name="subtitle">Join Gift Sri Lanka and start discovering thoughtful gifts for every occasion.</x-slot>

    <div class="rounded-[24px] bg-gradient-to-b from-white to-stone-50/80 shadow-[0_2px_16px_-4px_rgba(0,0,0,0.06),0_6px_32px_-8px_rgba(0,0,0,0.04)] border border-stone-100 p-10 opacity-0 animate-fade-in-up" style="animation-duration: 0.5s; animation-fill-mode: forwards;" x-data="{ showPassword: false, showPasswordConfirmation: false }">
        {{-- Header --}}
        <p class="text-xs font-medium tracking-wide text-stone-400 uppercase mb-1">Welcome to GiftSriLanka</p>
        <h2 class="font-display text-[28px] font-bold text-[#1F2937] tracking-tight">Create Your Account</h2>
        <p class="mt-2 text-sm text-stone-500">Join us and start your gifting journey.</p>
        <div class="mt-6 mb-8 h-px w-12 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full" aria-hidden="true"></div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            {{-- Full Name --}}
            <div class="relative" x-data="{ floated: false }" x-init="$nextTick(() => { floated = !!$refs.nameInput?.value })">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input x-ref="nameInput" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           @focus="floated = true" @blur="floated = !!$refs.nameInput?.value"
                           class="w-full h-[52px] pl-11 pr-4 rounded-[14px] border border-[#E5E7EB] bg-white/80 text-[#1F2937] placeholder-stone-400 focus:outline-none focus:border-[#10B981] focus:ring-[3px] focus:ring-[#10B981]/20 transition-all duration-300"
                           placeholder=" ">
                    <label for="name" class="absolute left-11 pointer-events-none transition-all duration-300"
                           :class="floated ? 'top-0 -translate-y-full text-xs text-stone-500 mt-0' : 'top-1/2 -translate-y-1/2 text-[15px] text-stone-400'"
                           style="transition-duration: 0.3s;">Full Name</label>
                </div>
                @error('name')
                    <p class="mt-1.5 text-sm text-[#EF4444]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="relative" x-data="{ floated: false }" x-init="$nextTick(() => { floated = !!$refs.emailInput?.value })">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input x-ref="emailInput" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           @focus="floated = true" @blur="floated = !!$refs.emailInput?.value"
                           class="w-full h-[52px] pl-11 pr-4 rounded-[14px] border border-[#E5E7EB] bg-white/80 text-[#1F2937] placeholder-stone-400 focus:outline-none focus:border-[#10B981] focus:ring-[3px] focus:ring-[#10B981]/20 transition-all duration-300"
                           placeholder=" ">
                    <label for="email" class="absolute left-11 pointer-events-none transition-all duration-300"
                           :class="floated ? 'top-0 -translate-y-full text-xs text-stone-500 mt-0' : 'top-1/2 -translate-y-1/2 text-[15px] text-stone-400'"
                           style="transition-duration: 0.3s;">Email</label>
                </div>
                @error('email')
                    <p class="mt-1.5 text-sm text-[#EF4444]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone (optional) --}}
            <div class="relative" x-data="{ floated: false }" x-init="$nextTick(() => { floated = !!$refs.phoneInput?.value })">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input x-ref="phoneInput" id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                           @focus="floated = true" @blur="floated = !!$refs.phoneInput?.value"
                           class="w-full h-[52px] pl-11 pr-4 rounded-[14px] border border-[#E5E7EB] bg-white/80 text-[#1F2937] placeholder-stone-400 focus:outline-none focus:border-[#10B981] focus:ring-[3px] focus:ring-[#10B981]/20 transition-all duration-300"
                           placeholder=" ">
                    <label for="phone" class="absolute left-11 pointer-events-none transition-all duration-300"
                           :class="floated ? 'top-0 -translate-y-full text-xs text-stone-500 mt-0' : 'top-1/2 -translate-y-1/2 text-[15px] text-stone-400'"
                           style="transition-duration: 0.3s;">Phone <span class="font-normal text-stone-400">(optional)</span></label>
                </div>
                @error('phone')
                    <p class="mt-1.5 text-sm text-[#EF4444]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="relative" x-data="{ floated: false }" x-init="$nextTick(() => { floated = !!$refs.passInput?.value })">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input x-ref="passInput" id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                           @focus="floated = true" @blur="floated = !!$refs.passInput?.value"
                           class="w-full h-[52px] pl-11 pr-12 rounded-[14px] border border-[#E5E7EB] bg-white/80 text-[#1F2937] placeholder-stone-400 focus:outline-none focus:border-[#10B981] focus:ring-[3px] focus:ring-[#10B981]/20 transition-all duration-300"
                           placeholder=" ">
                    <label for="password" class="absolute left-11 pointer-events-none transition-all duration-300"
                           :class="floated ? 'top-0 -translate-y-full text-xs text-stone-500 mt-0' : 'top-1/2 -translate-y-1/2 text-[15px] text-stone-400'"
                           style="transition-duration: 0.3s;">Password</label>
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-stone-400 hover:text-stone-600 transition" aria-label="Toggle password visibility">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <p class="mt-1.5 text-xs text-stone-500">At least 8 characters with a number, uppercase, lowercase, and symbol (e.g. !@#$%).</p>
                @error('password')
                    <p class="mt-1.5 text-sm text-[#EF4444]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="relative" x-data="{ floated: false }" x-init="$nextTick(() => { floated = !!$refs.confirmInput?.value })">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input x-ref="confirmInput" id="password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                           @focus="floated = true" @blur="floated = !!$refs.confirmInput?.value"
                           class="w-full h-[52px] pl-11 pr-12 rounded-[14px] border border-[#E5E7EB] bg-white/80 text-[#1F2937] placeholder-stone-400 focus:outline-none focus:border-[#10B981] focus:ring-[3px] focus:ring-[#10B981]/20 transition-all duration-300"
                           placeholder=" ">
                    <label for="password_confirmation" class="absolute left-11 pointer-events-none transition-all duration-300"
                           :class="floated ? 'top-0 -translate-y-full text-xs text-stone-500 mt-0' : 'top-1/2 -translate-y-1/2 text-[15px] text-stone-400'"
                           style="transition-duration: 0.3s;">Confirm Password</label>
                    <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-stone-400 hover:text-stone-600 transition" aria-label="Toggle password visibility">
                        <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPasswordConfirmation" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full h-[52px] rounded-[14px] bg-gradient-to-r from-[#0d4d2a] to-[#059669] text-white text-sm font-semibold shadow-[0_2px_12px_-2px_rgba(16,185,129,0.4)] hover:shadow-[0_8px_24px_-4px_rgba(16,185,129,0.35)] hover:-translate-y-[3px] active:translate-y-0 active:shadow-[0_2px_8px_-2px_rgba(16,185,129,0.3)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#10B981] transition-all duration-300">
                Create Account
            </button>
        </form>

        <div class="relative mt-6">
            <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-stone-200"></div></div>
            <div class="relative flex justify-center">
                <span class="bg-white px-3 text-xs font-medium text-stone-400 uppercase tracking-wider">Or sign up with</span>
            </div>
        </div>
        <a href="{{ route('google.redirect') }}" class="mt-6 flex items-center justify-center gap-2 w-full h-[52px] rounded-[14px] border border-stone-300 bg-white text-stone-700 font-medium text-sm hover:bg-stone-50 transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Google
        </a>

        <p class="mt-8 pt-6 border-t border-stone-100 text-center text-sm text-stone-500">
            Already have an account? <a href="{{ route('login') }}" class="font-semibold text-[#10B981] hover:text-[#059669] transition">Login</a>
        </p>
    </div>
</x-guest-layout>
