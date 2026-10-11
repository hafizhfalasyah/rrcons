@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Services'))

@section('content')
    @include('back.partials.text-items', [
        'items' => $services,
        'title' => __('Services') . ' — ' . __('Home Page'),
        'icon' => 'layers',
        'description' => __('The list of services offered by RR Consultant, shown on the home page.'),
        'location' => __('Home Page') . ' — ' . __('Services'),
        'previewUrl' => url('/'),
        'guide' => [__('Each service has a name and a short description.')],
        'updateRoute' => 'home.service.update',
        'headingRoute' => 'home.menu',
        'prefix' => 'edit-home-service',
        'nameField' => 'name',
        'nameLabel' => __('Service name'),
        'descLabel' => __('Description'),
    ])
@endsection
