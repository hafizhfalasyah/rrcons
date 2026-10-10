@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Sosmed Section'))

@section('content')

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h1 class="h3 fw-bold text-uppercase">{{ __('Sosmed Section') }}</h1>
</div>

{{-- Flash & Error Messages --}}
@if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="table-responsive mt-4">
    <table class="table table-hover table-bordered align-middle shadow-sm">
        <thead class="table-primary text-center">
            <tr>
                <th style="width: 5%;">{{ __('No') }}</th>
                <th style="width: 15%;">{{ __('Video') }}</th>
                <th style="width: 15%;">{{ __('Title') }}</th>
                <th style="width: 20%;">{{ __('Sosmed Link') }}</th>
                <th style="width: 10%;">{{ __('Last Updated') }}</th>
                <th style="width: 10%;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sosmed as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td class="text-center">
                        @if ($item->video)
                            <video width="200" controls>
                                <source src="{{ asset('storage/' . $item->video) }}" type="video/mp4">
                                <source src="{{ asset('storage/' . $item->video) }}" type="video/mpeg">
                                <source src="{{ asset('storage/' . $item->video) }}" type="video/x-matroska"> <!-- untuk .mkv -->
                                <source src="{{ asset('storage/' . $item->video) }}" type="video/quicktime"> <!-- untuk .mov -->
                                {{ __('Your browser does not support the video tag.') }}
                            </video>
                        @else
                            <span class="text-muted">{{ __('No Video') }}</span>
                        @endif
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->sosmed_link }}</td>
                    <td>
                        {{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d F Y') : '-' }}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#update-sosmed-modal{{ $item->id }}">
                            <i data-feather="edit" class="icon-sm me-1"></i> {{ __('Edit') }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('back.home_menu.sosmed_section.updatemodal')

@endsection
