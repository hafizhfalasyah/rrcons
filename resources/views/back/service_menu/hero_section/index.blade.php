@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Main Banner'))

@section('content')
    @include('back.partials.hero-banner', [
        'heroes' => $heroes,
        'pageName' => __('RR Vision Page'),
        'previewUrl' => route('service'),
        'updateRoute' => 'service.hero.update',
        'headingRoute' => 'service.menu',
        'prefix' => 'edit-service-hero',
    ])
@endsection
