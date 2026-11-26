<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script>
        (function() {
            var theme = localStorage.getItem('adminTheme');
            if (theme === 'dark') document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-card { border-radius: 18px; box-shadow: 0 2px 12px -2px rgba(0,0,0,0.06), 0 4px 16px -4px rgba(0,0,0,0.04); }
        .dark .admin-card {
            background-color: rgba(41, 37, 36, 0.9) !important;
            border-color: rgba(87, 83, 78, 0.9) !important;
            box-shadow: 0 2px 12px -2px rgba(0,0,0,0.2), 0 4px 16px -4px rgba(0,0,0,0.15);
        }
        .admin-sidebar { box-shadow: 4px 0 24px -4px rgba(0,0,0,0.06); }
        .dark .admin-sidebar { box-shadow: 4px 0 24px -4px rgba(0,0,0,0.2); }
        .admin-input:focus, .admin-select:focus { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15); }
        .dark .admin-input:focus, .dark .admin-select:focus { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25); }
        .dark .admin-content select,
        .dark .admin-content select:focus,
        .dark .admin-content .admin-select,
        .dark .admin-content .admin-select:focus {
            background-color: rgb(28, 25, 23) !important;
            color: rgb(245, 245, 244) !important;
            border-color: rgb(87, 83, 78) !important;
        }
        .dark .admin-content select option,
        .dark .admin-content .admin-select option {
            background-color: rgb(28, 25, 23) !important;
            color: rgb(245, 245, 244) !important;
        }
        /* Hard fallback: if any admin blocks still use bg-white, force dark surface */
        .dark .admin-content .bg-white,
        .dark .admin-content [class*="bg-white"] {
            background-color: rgba(41, 37, 36, 0.9) !important;
        }
        .dark .admin-content .border-stone-200,
        .dark .admin-content [class*="border-stone-200"] {
            border-color: rgba(87, 83, 78, 0.9) !important;
        }
    </style>
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 antialiased font-sans transition-colors duration-200 overflow-x-hidden">
    <div x-data="adminLayout()" class="flex min-h-screen relative isolate z-10" x-init="initTheme()">
        {{-- Sidebar overlay (mobile) --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-stone-900/40 dark:bg-black/50 backdrop-blur-sm lg:hidden"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        @include('admin.partials.sidebar')

        <div class="flex-1 flex flex-col min-h-screen min-w-0 lg:ml-64">
            @include('admin.partials.header', ['title' => $__env->yieldContent('title') ?: 'Admin'])

            <main class="flex-1 p-6 lg:p-8 max-md:p-4 admin-content">
                @if(session('success'))
                    <div class="mb-6 rounded-2xl bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 text-primary-800 dark:text-primary-200 px-5 py-3 text-sm font-medium admin-card">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-6 rounded-2xl bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 text-danger-800 dark:text-danger-200 px-5 py-3 text-sm font-medium admin-card">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.data('adminLayout', function() {
                return {
                    sidebarOpen: false,
                    darkMode: document.documentElement.classList.contains('dark'),
                    initTheme() {
                        this.darkMode = document.documentElement.classList.contains('dark');
                    },
                    toggleDark() {
                        this.darkMode = !this.darkMode;
                        document.documentElement.classList.toggle('dark', this.darkMode);
                        localStorage.setItem('adminTheme', this.darkMode ? 'dark' : 'light');
                    }
                };
            });
        });
    </script>
</body>
</html>
