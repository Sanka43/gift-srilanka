import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                'card': '16px',
                'card-lg': '20px',
            },
            colors: {
                cream: {
                    50: '#fefdfb',
                    100: '#fdf9f3',
                    200: '#faf4eb',
                    300: '#f5ecdf',
                },
                luxury: {
                    gold: '#b8860b',
                    'gold-light': '#d4a84b',
                    'gold-dark': '#8b6914',
                    navy: '#0f172a',
                    'navy-light': '#1e293b',
                    emerald: '#047857',
                    'emerald-light': '#059669',
                },
                // Brand: Primary Green, Accent Yellow, Accent Red
                primary: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    dark: '#0d4d2a',
                },
                warning: {
                    50: '#fefce8',
                    100: '#fef9c3',
                    200: '#fef08a',
                    300: '#fde047',
                    400: '#facc15',
                    500: '#eab308',
                    600: '#ca8a04',
                    700: '#a16207',
                    800: '#854d0e',
                    900: '#713f12',
                },
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
            },
            boxShadow: {
                'luxury': '0 4px 24px -4px rgba(15, 23, 42, 0.08), 0 8px 48px -8px rgba(15, 23, 42, 0.06)',
                'luxury-lg': '0 12px 48px -12px rgba(15, 23, 42, 0.12), 0 24px 64px -16px rgba(15, 23, 42, 0.08)',
                'luxury-xl': '0 24px 64px -16px rgba(15, 23, 42, 0.14)',
                'soft': '0 2px 12px -2px rgba(0,0,0,0.06), 0 4px 16px -4px rgba(0,0,0,0.04)',
                'soft-lg': '0 8px 24px -4px rgba(0,0,0,0.08), 0 12px 32px -8px rgba(0,0,0,0.06)',
                'gold': '0 4px 20px -4px rgba(234, 179, 8, 0.35)',
                'green-glow': '0 0 0 2px rgba(34, 197, 94, 0.25), 0 4px 20px -4px rgba(34, 197, 94, 0.2)',
                'yellow-glow': '0 0 0 2px rgba(250, 204, 21, 0.4), 0 4px 20px -4px rgba(250, 204, 21, 0.15)',
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out forwards',
                'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                'float': 'float 6s ease-in-out infinite',
                'shimmer': 'shimmer 2s ease-in-out infinite',
                'chat-launcher': 'chatLauncher 3.2s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
                shimmer: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.7' },
                },
                chatLauncher: {
                    '0%, 100%': {
                        transform: 'translate3d(0, 0, 0) scale(1)',
                        filter: 'drop-shadow(0 6px 14px rgba(15, 23, 42, 0.1))',
                    },
                    '50%': {
                        transform: 'translate3d(0, -6px, 0) scale(1.03)',
                        filter: 'drop-shadow(0 12px 24px rgba(34, 197, 94, 0.2))',
                    },
                },
            },
            transitionTimingFunction: {
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
                'bounce-soft': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            },
        },
    },

    plugins: [forms],
};
