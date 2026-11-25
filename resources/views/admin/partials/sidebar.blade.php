@php
    $nav = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'orders'],
        ['route' => 'admin.payments.index', 'label' => 'Payments', 'icon' => 'credit-card'],
        ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => 'products'],
        ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'categories'],
        ['route' => 'admin.customers.index', 'label' => 'Customers', 'icon' => 'customers'],
        ['route' => 'admin.users.index', 'label' => 'Users & Admins', 'icon' => 'customers'],
        ['route' => 'admin.chats.index', 'label' => 'Chats', 'icon' => 'mail'],
        ['route' => 'admin.contacts.index', 'label' => 'Contacts', 'icon' => 'mail'],
        ['route' => 'admin.reviews.index', 'label' => 'Reviews', 'icon' => 'reviews'],
        ['route' => 'admin.stock-notifications.index', 'label' => 'Stock Notifications', 'icon' => 'bell'],
        ['route' => 'admin.reports.index', 'label' => 'Reports', 'icon' => 'chart'],
        ['route' => 'admin.settings.index', 'label' => 'Settings', 'icon' => 'cog'],
    ];
@endphp
<aside class="admin-sidebar fixed top-0 left-0 z-50 h-full w-64 flex-shrink-0 bg-white dark:bg-stone-900 border-r border-stone-200 dark:border-stone-700 transform transition-transform duration-300 ease-out lg:translate-x-0"
       :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    <div class="flex h-full flex-col">
        {{-- Logo --}}
        <div class="flex h-16 items-center justify-between border-b border-stone-100 dark:border-stone-700 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="{{ config('app.name') }}" class="h-8 w-auto object-contain" width="32" height="32">
            </a>
            <button type="button" @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-800 hover:text-stone-700 dark:hover:text-stone-200" aria-label="Close menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3" aria-label="Admin navigation">
            <ul class="space-y-0.5">
                @foreach($nav as $item)
                    @php
                        $active = request()->routeIs(str_replace('.index', '.*', $item['route']));
                    @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" @click="sidebarOpen = false"
                           class="admin-nav-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300' : 'text-stone-600 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-stone-800 hover:text-stone-800 dark:hover:text-stone-200' }}">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $active ? 'bg-primary-100 dark:bg-primary-800/50 text-primary-600 dark:text-primary-400' : 'bg-stone-100 dark:bg-stone-800 text-stone-500 dark:text-stone-400' }}">
    @include('admin.partials.sidebar-icon', ['name' => $item['icon']])
</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Back to store --}}
        <div class="border-t border-stone-100 dark:border-stone-700 p-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium text-stone-500 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-stone-800 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Store
            </a>
        </div>
    </div>
</aside>

