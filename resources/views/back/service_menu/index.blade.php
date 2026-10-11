@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Section Headings'))

@section('content')
    @include('back.partials.section-headings', [
        'menus' => $serviceMenus,
        'pageName' => __('RR Vision Page'),
        'previewUrl' => route('service'),
        'updateRoute' => 'service.menu.update',
        'prefix' => 'edit-service-menu',
        'sections' => [
            'heroSections' => [__('Main Banner'), 'service.hero'],
            'serviceSections' => [__('Services'), 'service.ourService'],
        ],
    ])
@endsection
