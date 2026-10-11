@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Section Headings'))

@section('content')
    @include('back.partials.section-headings', [
        'menus' => $rrfutureMenus,
        'pageName' => __('RR Future Tech Page'),
        'previewUrl' => route('rrfuture'),
        'updateRoute' => 'rrfuture.menu.update',
        'prefix' => 'edit-rrfuture-menu',
        'sections' => [
            'heroSections' => [__('Main Banner'), 'rrfuture.hero'],
            'productSections' => [__('Products'), 'rrfuture.product'],
        ],
    ])
@endsection
