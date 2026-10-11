@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Create Article'))

@section('content')
    <x-admin.page-header :title="__('Create Article')" icon="edit-3"
        :description="__('Write a new blog article. Fill in the Indonesian version first; the English version is optional.')">
        <x-slot:actions>
            <a href="{{ route('blog.articles.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                <i data-feather="arrow-left" class="icon-xs"></i> {{ __('Back to articles') }}
            </a>
        </x-slot:actions>
        <x-slot:guide>
            <li>{{ __('Fields marked with * are required. The web address of the article is created from the Indonesian title.') }}</li>
            <li>{{ __('Choose Private to save a draft, Publish now to show it immediately, or Schedule to publish it automatically later.') }}</li>
            <li>{{ __('Pictures inside the article can be added with the image button in the editor toolbar.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    @include('back.article.partials.form', [
        'action' => route('blog.articles.store'),
        'method' => 'POST',
    ])
@endsection
