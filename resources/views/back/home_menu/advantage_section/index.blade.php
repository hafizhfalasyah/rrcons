@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Advantages'))

@section('content')
    @include('back.partials.text-items', [
        'items' => $advantages,
        'title' => __('Advantages') . ' — ' . __('Home Page'),
        'icon' => 'award',
        'description' => __('The reasons to choose RR Consultant, shown on the home page.'),
        'location' => __('Home Page') . ' — ' . __('Advantages'),
        'previewUrl' => url('/'),
        'guide' => [__('Each advantage has a short title and a description.')],
        'updateRoute' => 'home.advantage.update',
        'headingRoute' => 'home.menu',
        'prefix' => 'edit-home-advantage',
        'nameField' => 'title',
        'nameLabel' => __('Title'),
        'descLabel' => __('Description'),
    ])
@endsection
