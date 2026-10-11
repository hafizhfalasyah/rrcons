@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Event Gallery'))

@php
    // tanggal 1 agar tidak "meloncat" bulan (mis. 31 Februari menjadi Maret)
    $monthName = fn ($month) => \Carbon\Carbon::create(2000, (int) $month, 1)->translatedFormat('F');
    $months = collect(range(1, 12))->mapWithKeys(fn ($month) => [$month => $monthName($month)]);

@endphp

@section('content')
    <x-admin.page-header :title="__('Event Gallery') . ' — ' . __('Home Page')" icon="camera"
        :description="__('Photos of RR Consultant events. Visitors can filter them by month and year.')"
        :location="__('Home Page') . ' — ' . __('Event Gallery')" :preview-url="url('/')">
        <x-slot:actions>
            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#create-gallery">
                <i data-feather="plus" class="icon-xs"></i> {{ __('Add photo') }}
            </button>
        </x-slot:actions>
        <x-slot:guide>
            <li>{{ __('Add one photo per event. Fill in the month and year the event took place.') }}</li>
            <li>{{ __('Photo: JPG, PNG or WEBP, max 2 MB. Landscape photos look best.') }}</li>
            <li>{{ __('Deleting a photo removes it from the website permanently.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="row g-3">
        @forelse ($gallery as $item)
            <div class="col-sm-6 col-lg-4 col-xxl-3">
                <div class="panel d-flex flex-column">
                    <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->title }}" class="w-100" style="height: 180px; object-fit: cover; border-radius: 1rem 1rem 0 0;">
                    <div class="panel-body d-flex flex-column flex-grow-1">
                        <span class="badge bg-info-soft text-primary border align-self-start mb-2">
                            <i data-feather="calendar" class="icon-xs me-1"></i>{{ $monthName($item->month) }} {{ $item->year }}
                        </span>
                        <div class="fw-semibold small"><x-admin.bilingual :id="$item->title_id" :en="$item->title_en" :limit="60" /></div>
                        @if (filled($item->desc_id))
                            <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($item->desc_id, 90) }}</div>
                        @endif
                        <div class="d-flex gap-2 mt-auto pt-3">
                            <button class="btn btn-primary btn-sm flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-gallery-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-gallery-{{ $item->id }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                <i data-feather="trash-2" class="icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state border rounded-3">
                    <p class="mb-2">{{ __('No photos yet.') }}</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create-gallery">{{ __('Add photo') }}</button>
                </div>
            </div>
        @endforelse
    </div>

    @include('back.home_menu.gallery_section.form', ['key' => 'create-gallery', 'item' => null, 'months' => $months])

    @foreach ($gallery as $item)
        @include('back.home_menu.gallery_section.form', ['key' => 'edit-gallery-' . $item->id, 'item' => $item, 'months' => $months])
        <x-admin.delete-modal :id="'delete-gallery-' . $item->id" :action="route('home.gallery.delete', $item->id)" :item-name="$item->title_id"
            :message="__('Delete this photo from the gallery?')" />
    @endforeach
@endsection
