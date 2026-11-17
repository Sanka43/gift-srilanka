@props(['status' => 'pending', 'size' => 'default'])

@php
    $status = $status ?? 'pending';
    $classes = match(strtolower($status)) {
        'pending'    => 'bg-stone-100 text-stone-800 dark:bg-stone-700 dark:text-stone-100',
        'processing' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
        'shipped'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'delivered'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
        'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'paid'       => 'bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300',
        'completed'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
        default      => 'bg-stone-100 text-stone-700 dark:bg-stone-700 dark:text-stone-100',
    };
    $sizeClass = $size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-sm';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex font-medium rounded-full {$sizeClass} {$classes}"]) }}>
    {{ ucfirst($status) }}
</span>
