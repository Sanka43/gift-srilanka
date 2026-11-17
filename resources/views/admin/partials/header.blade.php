<header class="sticky top-0 z-30 flex h-16 flex-shrink-0 items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-700 bg-white/95 dark:bg-stone-900/95 px-4 shadow-sm backdrop-blur sm:px-6 lg:px-8">
    <div class="flex min-w-0 flex-1 items-center gap-4">
        <button type="button" @click="sidebarOpen = true" class="lg:hidden flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-800 hover:text-stone-700 dark:hover:text-stone-200" aria-label="Open menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="truncate font-display text-lg font-semibold text-stone-800 dark:text-stone-100">{{ $title ?? 'Admin' }}</h1>
        {{-- Search --}}
        <form action="{{ route('admin.orders.index') }}" method="GET" class="hidden flex-1 max-w-md sm:block">
            <label for="admin-search" class="sr-only">Search</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400 dark:text-stone-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="search" id="admin-search" name="search" value="{{ request('search') }}" placeholder="Search orders, products..."
                       class="admin-input w-full rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50/80 dark:bg-stone-800/80 py-2.5 pl-10 pr-4 text-sm text-stone-800 dark:text-stone-200 placeholder-stone-400 dark:placeholder-stone-500 transition-colors focus:border-primary-400 focus:bg-white dark:focus:bg-stone-800 focus:outline-none">
            </div>
        </form>
    </div>

    <div class="flex flex-shrink-0 items-center gap-2">
        {{-- Theme toggle --}}
        <button type="button" @click="toggleDark()" class="flex h-10 w-10 items-center justify-center rounded-xl text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-800 hover:text-stone-700 dark:hover:text-stone-200 transition-colors" aria-label="Toggle dark mode">
            <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        {{-- Profile dropdown --}}
        <div class="relative" x-data="{ profileOpen: false }"
             @click.outside="profileOpen = false"
             @keydown.escape.window="profileOpen = false">
            <button type="button" @click="profileOpen = !profileOpen"
                    class="flex items-center gap-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-800 px-3 py-2 text-left transition-all duration-200 hover:bg-stone-50 dark:hover:bg-stone-700 hover:border-stone-300 dark:hover:border-stone-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25 focus:ring-offset-2 dark:focus:ring-offset-stone-900"
                    :aria-expanded="profileOpen"
                    aria-haspopup="true"
                    aria-label="User menu">
                <span class="hidden text-sm font-medium text-stone-700 dark:text-stone-300 sm:block">{{ auth()->user()->name }}</span>
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 ring-2 ring-white dark:ring-stone-800">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/' . str_replace('\\', '/', auth()->user()->avatar_path)) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <span class="text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    @endif
                </span>
                <svg class="h-4 w-4 flex-shrink-0 text-stone-400 dark:text-stone-500 transition-transform duration-200" :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="profileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 x-ref="profileMenu"
                 class="absolute right-0 top-full z-50 mt-2.5 w-52 origin-top-right rounded-2xl bg-white dark:bg-stone-800 py-1.5 shadow-xl ring-1 ring-stone-200/80 dark:ring-stone-600/80 focus:outline-none"
                 style="display: none;">
                <div class="px-4 py-2.5 border-b border-stone-100 dark:border-stone-700">
                    <p class="text-xs font-medium uppercase tracking-wider text-stone-400 dark:text-stone-500">Signed in as</p>
                    <p class="mt-0.5 truncate text-sm font-medium text-stone-800 dark:text-stone-200">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 dark:text-stone-300 transition-colors duration-150 hover:bg-stone-50 dark:hover:bg-stone-700/80 hover:text-stone-900 dark:hover:text-stone-100">
                    <svg class="h-4 w-4 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </a>
                <div class="my-1 border-t border-stone-100 dark:border-stone-700"></div>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-stone-700 dark:text-stone-300 transition-colors duration-150 hover:bg-stone-50 dark:hover:bg-stone-700/80 hover:text-danger-600 dark:hover:text-danger-400 focus:bg-stone-50 dark:focus:bg-stone-700/80 focus:outline-none focus:text-danger-600 dark:focus:text-danger-400">
                        <svg class="h-4 w-4 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
