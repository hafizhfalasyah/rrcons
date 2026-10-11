@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Section Headings'))

@section('content')
    @include('back.partials.section-headings', [
        'menus' => $homeMenus,
        'pageName' => __('Home Page'),
        'previewUrl' => url('/'),
        'updateRoute' => 'home.menu.update',
        'prefix' => 'edit-home-menu',
        'sections' => [
            'heroSections' => [__('Main Banner'), 'home.hero'],
            'aboutSections' => [__('About Us'), 'home.about'],
            'serviceSections' => [__('Services'), 'home.service'],
            'advantageSections' => [__('Advantages'), 'home.advantage'],
            'profileSections' => [__('Team Profiles'), 'home.profile'],
            'businessSections' => [__('Business Training'), 'home.business'],
            'sosmedSections' => [__('Social Media Videos'), 'home.sosmed'],
            'gallerySections' => [__('Event Gallery'), 'home.gallery'],
        ],
    ])
@endsection
