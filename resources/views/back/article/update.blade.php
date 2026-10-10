@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Edit Article'))

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 border-bottom pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h1 class="h3 fw-bold text-uppercase mb-0">{{ __('Edit Article') }}</h1>
        <div>{{ __('Current status') }}: @include('back.article.partials.status_badge', ['article' => $article])</div>
    </div>

    @include('back.article.partials.form', [
        'article' => $article,
        'action' => route('blog.articles.update', $article->id),
        'method' => 'PUT',
    ])
</div>
@endsection
