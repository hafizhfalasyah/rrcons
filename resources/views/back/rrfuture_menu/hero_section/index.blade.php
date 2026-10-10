@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Hero Section'))

@section('content')

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h1 class="h3 fw-bold text-uppercase">{{ __('Hero Section') }}</h1>
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
                <th style="width: 20%;">{{ __('Background Image') }}</th>
                <th style="width: 10%;">{{ __('Last Updated') }}</th>
                <th style="width: 10%;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($heroes as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td class="text-center">
                        @if ($item->bg_img)
                            <img src="{{ asset('storage/' . $item->bg_img) }}" alt="{{ __('Hero Image') }}" width="500">
                        @else
                            <span class="text-muted">{{ __('No Image') }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d F Y') : '-' }}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#update-hero-modal{{ $item->id }}">
                            <i data-feather="edit" class="icon-sm me-1"></i> {{ __('Edit') }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('back.rrfuture_menu.hero_section.updatemodal')

@endsection
