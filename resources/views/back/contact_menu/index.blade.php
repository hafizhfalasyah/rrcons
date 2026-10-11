@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Section Headings'))

@section('content')
    @include('back.partials.section-headings', [
        'menus' => $contactMenus,
        'pageName' => __('Contact Page'),
        'previewUrl' => route('contact'),
        'updateRoute' => 'contact.menu.update',
        'prefix' => 'edit-contact-menu',
        'sections' => [
            'heroSections' => [__('Main Banner'), 'contact.hero'],
            'contactSections' => [__('Contact Details'), 'contact.usContact'],
        ],
    ])
@endsection
