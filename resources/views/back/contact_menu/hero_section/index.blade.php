@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Main Banner'))

@section('content')
    @include('back.partials.hero-banner', [
        'heroes' => $heroes,
        'pageName' => __('Contact Page'),
        'previewUrl' => route('contact'),
        'updateRoute' => 'contact.hero.update',
        'headingRoute' => 'contact.menu',
        'prefix' => 'edit-contact-hero',
    ])
@endsection
