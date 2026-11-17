@if(isset($seoCategory) && $seoCategory)
@push('structured-data')
@php
    $baseUrl = rtrim(config('app.url'), '/');
    $shopCategoryUrl = url()->current();
    $breadcrumbList = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $baseUrl . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => $baseUrl . '/shop'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $seoCategory->name, 'item' => $shopCategoryUrl],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbList) !!}</script>
@endpush
@endif
