{{--
    Judul halaman admin + penjelasan singkat untuk admin non-teknis.
    Props : title, icon (feather), description, location (di mana konten tampil), previewUrl
    Slots : actions (tombol di kanan), guide (daftar <li> panduan singkat)
--}}
@props(['title', 'icon' => 'file-text', 'description' => null, 'location' => null, 'previewUrl' => null])

<div class="page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div class="d-flex gap-3 align-items-start min-w-0">
            <span class="page-header-icon"><i data-feather="{{ $icon }}"></i></span>
            <div class="min-w-0">
                <h1 class="fw-bold mb-1">{{ $title }}</h1>
                @if ($description)
                    <p class="text-muted mb-2">{{ $description }}</p>
                @endif
                @if ($location)
                    <span class="page-location">
                        <i data-feather="map-pin" class="icon-xs"></i> {{ __('Shown on the website:') }} <strong>{{ $location }}</strong>
                    </span>
                @endif
            </div>
        </div>

        @if ($previewUrl || isset($actions))
            <div class="d-flex flex-wrap gap-2">
                @if ($previewUrl)
                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i data-feather="external-link" class="icon-xs"></i> {{ __('View on website') }}
                    </a>
                @endif
                {{ $actions ?? '' }}
            </div>
        @endif
    </div>

    @isset($guide)
        <div class="guide-box mt-3">
            <div class="guide-title"><i data-feather="info" class="icon-sm"></i> {{ __('Quick guide') }}</div>
            <ul>{{ $guide }}</ul>
        </div>
    @endisset
</div>

<x-admin.alerts />
