<footer class="mt-auto relative overflow-hidden">
    {{-- Curved top edge --}}
    <div class="absolute left-0 right-0 top-0 h-8 sm:h-10 -translate-y-px" aria-hidden="true">
        <svg class="w-full h-full text-primary-800" viewBox="0 0 1440 64" fill="currentColor" preserveAspectRatio="none">
            <path d="M0 64V0c240 32 480 48 720 48s480-16 720-48v64H0z"/>
        </svg>
    </div>

    {{-- Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-primary-800 via-primary-800 to-primary-900" aria-hidden="true"></div>
    <div class="absolute inset-0 opacity-[0.06]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');" aria-hidden="true"></div>
    <div class="absolute top-1/4 left-0 w-[480px] h-[320px] rounded-full bg-warning-400/8 blur-3xl -translate-y-1/2 -translate-x-1/4" aria-hidden="true"></div>
    <div class="absolute bottom-1/4 right-0 w-[400px] h-[280px] rounded-full bg-primary-400/12 blur-3xl translate-y-1/2 translate-x-1/4" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-12 pb-0 max-md:px-5 max-md:pt-10">
        {{-- Main grid: Brand | Shop | Support | About + Social --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-6 py-6 sm:py-8 max-md:gap-8 max-md:py-8 max-sm:text-center">
            {{-- Brand column (wider on lg) --}}
            <div class="sm:col-span-2 lg:col-span-4 max-sm:flex max-sm:flex-col max-sm:items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-2">
                    <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="{{ config('app.name') }}" class="h-8 w-auto object-contain" width="32" height="32">
                    <!-- <span class="font-display font-bold text-white text-lg tracking-tight">{{ config('app.name') }}</span> -->
                </a>
                <p class="text-sm text-white/75 leading-snug max-w-xs max-sm:mx-auto">
                    Curated gifts for every occasion. Premium delivery across Colombo.
                </p>
                <div class="mt-3 flex flex-col gap-1.5 text-sm text-white/70 max-sm:items-center">
                    <a href="mailto:{{ config('contact.email') }}" class="inline-flex items-center gap-2 hover:text-warning-400 transition-colors">
                        <svg class="w-4 h-4 shrink-0 text-warning-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ config('contact.email') }}
                    </a>
                    <a href="tel:{{ preg_replace('/\s+/', '', config('contact.phone')) }}" class="inline-flex items-center gap-2 hover:text-warning-400 transition-colors">
                        <svg class="w-4 h-4 shrink-0 text-warning-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ config('contact.phone') }}
                    </a>
                    <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" class="inline-flex items-center gap-2 hover:text-warning-400 transition-colors">
                        <svg class="w-4 h-4 shrink-0 text-warning-400/80" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.8 11.8 0 0012.05 0C5.5 0 .18 5.32.18 11.87c0 2.1.55 4.15 1.6 5.97L0 24l6.34-1.66a11.84 11.84 0 005.7 1.45h.01c6.55 0 11.87-5.32 11.87-11.87 0-3.17-1.23-6.15-3.4-8.44zM12.05 21.7a9.8 9.8 0 01-5-1.37l-.36-.21-3.76.98 1-3.67-.24-.38a9.78 9.78 0 01-1.5-5.18c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.02 6.96 2.88a9.77 9.77 0 012.88 6.95c0 5.43-4.42 9.83-9.82 9.83zm5.39-7.36c-.3-.15-1.77-.88-2.04-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.48-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.67-1.62-.92-2.22-.24-.58-.48-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5 0 1.48 1.08 2.9 1.23 3.1.15.2 2.12 3.24 5.14 4.54.72.3 1.28.48 1.72.62.72.23 1.37.2 1.88.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.43-.08-.13-.28-.2-.58-.35z"/></svg>
                        {{ config('contact.whatsapp') }}
                    </a>
                    <span class="inline-flex items-center gap-2 text-white/70">
                        <svg class="w-4 h-4 shrink-0 text-warning-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ config('contact.address') }}
                    </span>
                </div>
            </div>

            {{-- Shop --}}
            <div class="lg:col-span-2 max-sm:flex max-sm:flex-col max-sm:items-center">
                <h4 class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-2">Shop</h4>
                <nav class="flex flex-col gap-2 text-sm max-sm:items-center" aria-label="Footer shop links">
                    <a href="{{ route('home') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Home
                    </a>
                    <a href="{{ route('shop.index') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>All products
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Cart
                    </a>
                </nav>
            </div>

            {{-- Support --}}
            <div class="lg:col-span-2 max-sm:flex max-sm:flex-col max-sm:items-center">
                <h4 class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-2">Support</h4>
                <nav class="flex flex-col gap-2 text-sm max-sm:items-center" aria-label="Footer support links">
                    <a href="{{ route('page.contact') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Contact
                    </a>
                    <a href="{{ route('page.shipping') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Shipping
                    </a>
                    <a href="{{ route('page.return') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Returns
                    </a>
                    <a href="{{ route('page.faq') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>FAQ
                    </a>
                </nav>
            </div>

            {{-- About + Social --}}
            <div class="sm:col-span-2 lg:col-span-4 max-sm:flex max-sm:flex-col max-sm:items-center">
                <h4 class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-2">About</h4>
                <nav class="flex flex-col gap-2 text-sm mb-4 max-sm:items-center" aria-label="Footer about links">
                    <a href="{{ route('page.blog') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Blog
                    </a>
                    <a href="{{ route('page.team') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Meet the team
                    </a>
                    <a href="{{ route('page.contact') }}" class="text-white/75 hover:text-warning-400 transition-colors inline-flex items-center gap-2 group">
                        <span class="w-0 group-hover:w-1 h-px bg-warning-400 transition-all duration-200 shrink-0"></span>Contact us
                    </a>
                </nav>
                <p class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-2">Follow us</p>
                <div class="flex items-center gap-1.5 max-sm:justify-center" aria-label="Social links">
                    <a href="https://web.facebook.com/profile.php?id=61580788319962&mibextid=wwXIfr&rdid=qeJTariyALnp7n6v&share_url=https%3A%2F%2Fweb.facebook.com%2Fshare%2F18Lfx9taWM%2F%3Fmibextid%3DwwXIfr%26_rdc%3D1%26_rdr#" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-white/10 text-white/80 hover:bg-warning-400 hover:text-primary-900 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-warning-400 focus:ring-offset-2 focus:ring-offset-primary-800" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://www.tiktok.com/@giftsrilanka_official" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-white/10 text-white/80 hover:bg-warning-400 hover:text-primary-900 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-warning-400 focus:ring-offset-2 focus:ring-offset-primary-800" aria-label="TikTok">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.589 6.686a4.793 4.793 0 01-3.77-4.237V2h-2.95v13.184a2.723 2.723 0 11-2.722-2.713c.227 0 .448.03.659.085V9.56a5.67 5.67 0 00-.659-.038A5.676 5.676 0 1015.82 15.2V8.51a7.722 7.722 0 004.524 1.46V7.02a4.84 4.84 0 01-.755-.334z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/giftsrilanka_?igsh=c3V2YW5ycWdnb212" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-white/10 text-white/80 hover:bg-warning-400 hover:text-primary-900 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-warning-400 focus:ring-offset-2 focus:ring-offset-primary-800" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2C4.574 2 2 4.574 2 7.75v8.5C2 19.426 4.574 22 7.75 22h8.5C19.426 22 22 19.426 22 16.25v-8.5C22 4.574 19.426 2 16.25 2h-8.5zm0 2h8.5A3.75 3.75 0 0120 7.75v8.5A3.75 3.75 0 0116.25 20h-8.5A3.75 3.75 0 014 16.25v-8.5A3.75 3.75 0 017.75 4zm8.75 1a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5zM12 7a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 max-md:py-5 max-md:gap-4 border-t border-white/15 max-sm:items-center max-sm:text-center">
            <p class="text-sm text-white/60">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <nav class="flex items-center gap-6 text-sm max-sm:justify-center" aria-label="Legal">
                <a href="{{ route('page.terms') }}" class="text-white/60 hover:text-warning-400 transition-colors">Terms</a>
                <a href="{{ route('page.privacy') }}" class="text-white/60 hover:text-warning-400 transition-colors">Privacy</a>
            </nav>
        </div>
    </div>
</footer>
