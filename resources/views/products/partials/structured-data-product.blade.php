@push('structured-data')
@php
    $baseUrl = rtrim(config('app.url'), '/');
    $productUrl = $canonical ?? $baseUrl . request()->getRequestUri();
    $imageUrl = isset($metaImage) ? (str_starts_with($metaImage, 'http') ? $metaImage : $baseUrl . '/' . ltrim($metaImage, '/')) : null;
    if (!$imageUrl && $product->images->isNotEmpty()) {
        $imageUrl = $product->images->first()->public_url;
    }
    $breadcrumbList = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $baseUrl . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => $baseUrl . '/shop'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => $productUrl],
        ],
    ];
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->description ? \Illuminate\Support\Str::limit(strip_tags($product->description), 500) : null,
        'url' => $productUrl,
        // 'image' => $imageUrl,
        'sku' => (string) $product->id,
        'offers' => [
            '@type' => 'Offer',
            'url' => $productUrl,
            'priceCurrency' => 'LKR',
            'price' => (float) $product->effective_price,
            'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ],
    ];
    if ($product->reviews->isNotEmpty()) {
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => round($product->reviews->avg('rating'), 1),
            'reviewCount' => $product->reviews->count(),
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbList) !!}</script>
<script type="application/ld+json">{!! json_encode($productSchema) !!}</script>
@endpush
