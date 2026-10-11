{{-- Prolog XML sengaja dipecah: Blade memakai tokenizer PHP, jadi tanda kurung sudut + tanda tanya akan dibaca sebagai tag PHP bila short_open_tag=On --}}{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ config('seo.organization.name') }} — Blog</title>
        <link>{{ \App\Support\Seo::localizedUrl(route('blog')) }}</link>
        <description>{{ __('blog.seo.index.description') }}</description>
        <language>{{ \App\Support\Seo::hreflang() }}</language>
        <lastBuildDate>{{ $updated->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('feed', ['lang' => app()->getLocale()]) }}" rel="self" type="application/rss+xml"/>
        <image>
            <url>{{ \App\Support\Seo::absoluteAsset(config('seo.organization.logo')) }}</url>
            <title>{{ config('seo.organization.name') }} — Blog</title>
            <link>{{ \App\Support\Seo::localizedUrl(route('blog')) }}</link>
        </image>
@foreach ($articles as $article)
        <item>
            <title>{{ $article->title }}</title>
            <link>{{ \App\Support\Seo::localizedUrl(route('detail_blog', $article->slug)) }}</link>
            <guid isPermaLink="true">{{ route('detail_blog', $article->slug) }}</guid>
            <pubDate>{{ ($article->publish_date ?? $article->created_at)->toRssString() }}</pubDate>
@if ($article->Creator)
            <dc:creator>{{ $article->Creator->name }}</dc:creator>
@endif
@if ($article->Category)
            <category>{{ $article->Category->name }}</category>
@endif
            <description>{{ $article->seoDescription() }}</description>
            <media:content url="{{ \App\Support\Seo::absoluteAsset('storage/' . $article->img) }}" medium="image"/>
        </item>
@endforeach
    </channel>
</rss>
