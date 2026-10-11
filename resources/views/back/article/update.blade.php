@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Edit Article'))

@section('content')
    <x-admin.page-header :title="__('Edit Article')" icon="edit-3"
        :description="\Illuminate\Support\Str::limit($article->title_id, 90)">
        <x-slot:actions>
            <span class="small d-inline-flex align-items-center gap-1">{{ __('Current status') }}: @include('back.article.partials.status_badge', ['article' => $article])</span>
            <a href="{{ route('blog.articles.show', $article->id) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                <i data-feather="arrow-left" class="icon-xs"></i> {{ __('Back') }}
            </a>
        </x-slot:actions>
        <x-slot:guide>
            <li>{{ __('If you change the Indonesian title, the article URL changes too; the old URL keeps working and redirects to the new one.') }}</li>
            <li>{{ __('Leave the image field empty to keep the current cover image.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    @include('back.article.partials.form', [
        'article' => $article,
        'action' => route('blog.articles.update', $article->id),
        'method' => 'PUT',
    ])
@endsection
