@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Products'))

@section('content')
    @include('back.partials.text-items', [
        'items' => $products,
        'title' => __('Products') . ' — ' . __('RR Future Tech Page'),
        'icon' => 'package',
        'description' => __('The RR Future Tech product types shown on the RR Future Tech page.'),
        'location' => __('RR Future Tech Page') . ' — ' . __('Products'),
        'previewUrl' => route('rrfuture'),
        'guide' => [__('Each product has a name and a short description.')],
        'updateRoute' => 'rrfuture.product.update',
        'headingRoute' => 'rrfuture.menu',
        'prefix' => 'edit-rrfuture-product',
        'nameField' => 'name',
        'nameLabel' => __('Product name'),
        'descLabel' => __('Description'),
    ])
@endsection
