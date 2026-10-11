@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Detail Article'))

@section('content')
@php($status = $article->publication_status)
<div class="container-fluid px-4 py-4">
    <div class="mb-4 border-bottom pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h1 class="h3 fw-bold text-uppercase mb-0">{{ __('Detail Article') }}</h1>
        <div class="d-flex gap-2">
            @if ($status === \App\Models\Blog\Article::PUBLICATION_PUBLISHED)
                <a href="{{ route('detail_blog', $article->slug) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i data-feather="external-link" class="icon-sm me-1"></i> {{ __('View on website') }}
                </a>
            @endif
            <a href="{{ route('blog.articles.edit', $article->id) }}" class="btn btn-primary btn-sm d-flex align-items-center">
                <i data-feather="edit" class="icon-sm me-1"></i> {{ __('Edit') }}
            </a>
        </div>
    </div>

    {{-- Penjelasan status saat ini --}}
    <div class="alert {{ ['private' => 'alert-danger', 'scheduled' => 'alert-warning', 'published' => 'alert-primary'][$status] }} d-flex align-items-start gap-2">
        <div>@include('back.article.partials.status_badge', ['article' => $article])</div>
        <div class="small">
            @if ($status === \App\Models\Blog\Article::PUBLICATION_PRIVATE)
                {{ __('This article is private and is not visible on the website. Edit it and choose "Publish now" or "Schedule" to make it visible.') }}
            @elseif ($status === \App\Models\Blog\Article::PUBLICATION_SCHEDULED)
                {{ __('This article is hidden and will be published automatically on :date (:relative).', [
                    'date' => $article->publish_date->translatedFormat('d F Y H:i'),
                    'relative' => $article->publish_date->diffForHumans(),
                ]) }}
            @else
                {{ __('This article is visible on the website since :date.', ['date' => optional($article->publish_date)->translatedFormat('d F Y H:i') ?? '-']) }}
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4 mt-4">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th class="bg-light" style="width: 200px;">{{ __('Title') }} (ID)</th>
                    <td>{{ $article->title_id }}</td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Title') }} (EN)</th>
                    <td>
                        @if (filled($article->title_en))
                            {{ $article->title_en }}
                        @else
                            <span class="text-muted fst-italic">{{ __('Empty — the Indonesian version will be shown.') }}</span>
                        @endif
                    </td>
                </tr>
                @foreach (['id' => 'ID', 'en' => 'EN'] as $lang => $code)
                    <tr>
                        <th class="bg-light">{{ __('SEO title & description') }} ({{ $code }})</th>
                        <td>
                            @if (filled($article->{'meta_title_' . $lang}) || filled($article->{'meta_description_' . $lang}))
                                <div class="fw-semibold">{{ $article->{'meta_title_' . $lang} ?: '—' }}</div>
                                <div class="small text-muted">{{ $article->{'meta_description_' . $lang} ?: '—' }}</div>
                            @else
                                <span class="text-muted fst-italic">{{ __('Automatic (article title & content)') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th class="bg-light">{{ __('Article URL') }}</th>
                    <td>
                        <code>/blog/{{ $article->slug }}</code>
                        @if ($article->slugRedirects->isNotEmpty())
                            <div class="small text-muted mt-2">{{ __('Old URLs that redirect here:') }}</div>
                            <ul class="small mb-0">
                                @foreach ($article->slugRedirects->sortByDesc('updated_at') as $redirect)
                                    <li><code>/blog/{{ $redirect->slug }}</code></li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Category') }}</th>
                    <td>{{ $article->Category?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Image') }}</th>
                    <td>
                        <a href="{{ asset('storage/' . $article->img) }}" target="_blank">
                            <img src="{{ asset('storage/' . $article->img) }}" class="img-fluid rounded" style="max-width: 50%;" alt="{{ $article->title_id }}">
                        </a>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Views') }}</th>
                    <td>{{ $article->views }}x</td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Publish Date') }}</th>
                    <td>@include('back.article.partials.publish_date', ['article' => $article])</td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Created By') }}</th>
                    <td>{{ $article->Creator?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">{{ __('Last Edited By') }}</th>
                    <td>
                        {{ $article->User?->name ?? '-' }}
                        <small class="text-muted">({{ $article->updated_at?->translatedFormat('d F Y H:i') }})</small>
                    </td>
                </tr>
            </table>

            <ul class="nav nav-tabs mt-4" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#show-id" type="button">{{ __('Indonesian') }}</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#show-en" type="button">{{ __('English') }}</button></li>
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom p-3">
                <div class="tab-pane fade show active" id="show-id">{!! \App\Support\HtmlSanitizer::clean($article->desc_id) !!}</div>
                <div class="tab-pane fade" id="show-en">
                    @if (filled($article->desc_en))
                        {!! \App\Support\HtmlSanitizer::clean($article->desc_en) !!}
                    @else
                        <span class="text-muted fst-italic">{{ __('Empty — the Indonesian version will be shown.') }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('blog.articles.index') }}" class="btn btn-danger btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                    <i data-feather="chevron-left" class="icon-sm"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
