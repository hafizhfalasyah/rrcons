@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Create Article'))

@section('content')
<div class="container py-4">
    <div class="mb-4 border-bottom pb-2">
        <h1 class="h3 fw-bold text-uppercase">{{ __('Create Article') }}</h1>
    </div>

    @include('back.article.partials.form', [
        'action' => route('blog.articles.store'),
        'method' => 'POST',
    ])
</div>
@endsection
