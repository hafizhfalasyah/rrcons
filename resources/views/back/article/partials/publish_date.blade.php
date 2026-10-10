@php($status = $article->publication_status)
@if ($status === \App\Models\Blog\Article::PUBLICATION_PRIVATE)
    <span class="text-muted">—</span>
    <div class="small text-muted">{{ __('Not published') }}</div>
@elseif ($status === \App\Models\Blog\Article::PUBLICATION_SCHEDULED)
    {{ $article->publish_date->translatedFormat('d F Y H:i') }}
    <div class="small text-warning-emphasis">{{ __('Scheduled') }} · {{ $article->publish_date->diffForHumans() }}</div>
@else
    {{ optional($article->publish_date)->translatedFormat('d F Y H:i') ?? '-' }}
@endif
