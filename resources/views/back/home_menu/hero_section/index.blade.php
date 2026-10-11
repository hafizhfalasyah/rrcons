@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Main Banner'))

@section('content')
    @include('back.partials.hero-banner', [
        'heroes' => $heroes,
        'pageName' => __('Home Page'),
        'previewUrl' => url('/'),
        'updateRoute' => 'home.hero.update',
        'headingRoute' => 'home.menu',
        'prefix' => 'edit-home-hero',
    ])
@endsection
