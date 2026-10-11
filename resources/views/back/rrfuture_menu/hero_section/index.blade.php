@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Main Banner'))

@section('content')
    @include('back.partials.hero-banner', [
        'heroes' => $heroes,
        'pageName' => __('RR Future Tech Page'),
        'previewUrl' => route('rrfuture'),
        'updateRoute' => 'rrfuture.hero.update',
        'headingRoute' => 'rrfuture.menu',
        'prefix' => 'edit-rrfuture-hero',
    ])
@endsection
