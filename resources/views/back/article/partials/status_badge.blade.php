@php($status = $article->publication_status)
@if ($status === \App\Models\Blog\Article::PUBLICATION_PRIVATE)
    <span class="badge bg-danger px-3 py-2" title="{{ __('Not visible on the website.') }}">
        <i class="bi bi-lock-fill me-1"></i>{{ __('Private') }}
    </span>
@elseif ($status === \App\Models\Blog\Article::PUBLICATION_SCHEDULED)
    <span class="badge bg-warning text-dark px-3 py-2" title="{{ __('Will be published automatically on :date.', ['date' => $article->publish_date->translatedFormat('d F Y H:i')]) }}">
        <i class="bi bi-clock-fill me-1"></i>{{ __('Scheduled') }}
    </span>
@else
    <span class="badge bg-primary px-3 py-2" title="{{ __('Visible on the website.') }}">
        <i class="bi bi-globe me-1"></i>{{ __('Published') }}
    </span>
@endif
