@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Services'))

@section('content')
    @include('back.partials.text-items', [
        'items' => $ours,
        'title' => __('Services') . ' — ' . __('RR Vision Page'),
        'icon' => 'layers',
        'description' => __('The RR Vision services shown on the RR Vision page.'),
        'location' => __('RR Vision Page') . ' — ' . __('Services'),
        'previewUrl' => route('service'),
        'guide' => [__('Each service has a name and a short description.')],
        'updateRoute' => 'service.ourService.update',
        'headingRoute' => 'service.menu',
        'prefix' => 'edit-service-our',
        'nameField' => 'name',
        'nameLabel' => __('Service name'),
        'descLabel' => __('Description'),
    ])
@endsection
