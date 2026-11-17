@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'canonical' => null,
    'noindex' => false,
    'type' => 'website',
])

@php
    $siteName = config('seo.site_name', config('app.name'));
    $baseUrl = rtrim(config('app.url'), '/');
    $title = $title ?? config('seo.default_title') . ' | ' . $siteName;
    $description = $description ?? config('seo.default_description');
    $image = $image ?? $baseUrl . '/' . ltrim(config('seo.default_image'), '/');
    if ($image && !str_starts_with($image, 'http')) {
        $image = $baseUrl . '/' . ltrim($image, '/');
    }
    $canonical = $canonical ?? $baseUrl . request()->getRequestUri();
    if ($canonical && !str_starts_with($canonical, 'http')) {
        $canonical = $baseUrl . '/' . ltrim($canonical, '/');
    }
@endphp

@if($noindex)
<meta name="robots" content="noindex, nofollow">
@else
<meta name="description" content="{{ Str::limit(strip_tags($description), 160) }}">

<link rel="canonical" href="{{ $canonical }}">

<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($description), 200) }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($description), 200) }}">
<meta name="twitter:image" content="{{ $image }}">
@endif
