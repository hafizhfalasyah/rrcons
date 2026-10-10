{{-- Meta SEO, Open Graph, Twitter, hreflang, GEO & JSON-LD. Variabel: $seo (App\Support\Seo) --}}
@php
    $geo = config('seo.geo');
    $currentLocale = app()->getLocale();
@endphp
<title>{{ $seo->fullTitle() }}</title>
<meta name="description" content="{{ $seo->description }}">
@if ($seo->keywords)
<meta name="keywords" content="{{ $seo->keywords }}">
@endif
<meta name="author" content="{{ $seo->article['author'] ?? config('seo.organization.name') }}">
<meta name="robots" content="{{ $seo->index ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, follow' }}">
<link rel="canonical" href="{{ $seo->canonical() }}">
@foreach ($seo->alternates() as $hreflang => $url)
<link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $url }}">
@endforeach
<link rel="alternate" type="application/rss+xml" title="{{ config('seo.organization.name') }} — Blog" href="{{ route('feed', ['lang' => $currentLocale]) }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ config('seo.organization.name') }}">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:title" content="{{ $seo->fullTitle() }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:url" content="{{ $seo->canonical() }}">
<meta property="og:image" content="{{ $seo->imageUrl() }}">
<meta property="og:image:alt" content="{{ $seo->imageAlt ?? $seo->fullTitle() }}">
<meta property="og:locale" content="{{ \App\Support\Seo::ogLocale() }}">
@foreach (array_keys(config('seo.locales')) as $locale)
@if ($locale !== $currentLocale)
<meta property="og:locale:alternate" content="{{ \App\Support\Seo::ogLocale($locale) }}">
@endif
@endforeach
@foreach ($seo->article as $property => $value)
<meta property="article:{{ $property }}" content="{{ $value }}">
@endforeach

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->fullTitle() }}">
<meta name="twitter:description" content="{{ $seo->description }}">
<meta name="twitter:image" content="{{ $seo->imageUrl() }}">
@if (config('seo.twitter_site'))
<meta name="twitter:site" content="{{ config('seo.twitter_site') }}">
@endif

{{-- GEO (lokasi bisnis) --}}
<meta name="geo.region" content="{{ $geo['region'] }}">
@if ($geo['locality'])
<meta name="geo.placename" content="{{ $geo['locality'] }}">
@endif
@if ($geo['latitude'] && $geo['longitude'])
<meta name="geo.position" content="{{ $geo['latitude'] }};{{ $geo['longitude'] }}">
<meta name="ICBM" content="{{ $geo['latitude'] }}, {{ $geo['longitude'] }}">
@endif

@if (config('seo.verification.google'))
<meta name="google-site-verification" content="{{ config('seo.verification.google') }}">
@endif
@if (config('seo.verification.bing'))
<meta name="msvalidate.01" content="{{ config('seo.verification.bing') }}">
@endif

<script type="application/ld+json">{!! $seo->jsonLd() !!}</script>
