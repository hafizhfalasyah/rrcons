{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($entries as $entry)
@foreach ($locales as $locale)
    <url>
        <loc>{{ \App\Support\Seo::localizedUrl($entry['url'], $locale, $entry['query'] ?? []) }}</loc>
@foreach ($locales as $alternate)
        <xhtml:link rel="alternate" hreflang="{{ \App\Support\Seo::hreflang($alternate) }}" href="{{ \App\Support\Seo::localizedUrl($entry['url'], $alternate, $entry['query'] ?? []) }}"/>
@endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $entry['url'] . (!empty($entry['query']) ? '?' . http_build_query($entry['query']) : '') }}"/>
@if (!empty($entry['lastmod']))
        <lastmod>{{ $entry['lastmod']->toAtomString() }}</lastmod>
@endif
        <changefreq>{{ $entry['changefreq'] }}</changefreq>
        <priority>{{ $entry['priority'] }}</priority>
@if (!empty($entry['image']))
        <image:image>
            <image:loc>{{ $entry['image']['loc'] }}</image:loc>
            <image:title>{{ $entry['image']['title'] }}</image:title>
        </image:image>
@endif
    </url>
@endforeach
@endforeach
</urlset>
