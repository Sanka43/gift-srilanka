@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => rtrim(config('app.url'), '/') . '/#organization',
            'name' => config('seo.site_name', config('app.name')),
            'url' => rtrim(config('app.url'), '/'),
        ],
        [
            '@type' => 'WebSite',
            '@id' => rtrim(config('app.url'), '/') . '/#website',
            'url' => rtrim(config('app.url'), '/'),
            'name' => config('seo.site_name', config('app.name')),
            'description' => config('seo.default_description'),
            'publisher' => ['@id' => rtrim(config('app.url'), '/') . '/#organization'],
        ],
    ],
]) !!}
</script>
@endpush
