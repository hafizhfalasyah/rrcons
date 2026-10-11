<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>@yield('title')</title>

        <!-- CSRF Token for Laravel -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Font (sama dengan website publik) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <!-- Feather Icon -->
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>

        <!-- Icon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('front/image/logo.png') }}">

        <!-- Tampilan panel admin -->
        <link rel="stylesheet" href="{{ asset_version('back/css/admin.css') }}">

        <!-- CSS Tambahan per-halaman (opsional) -->
        @stack('css')
    </head>

    <body>
        @include('partials.page-loader')

        @php
            $currentLang = app()->getLocale();
            $flag = $currentLang === 'en' ? 'us' : 'id';
            $langLabel = $currentLang === 'en' ? 'English' : 'Indonesia';
            $authUser = auth()->user();
        @endphp

        <!-- Header -->
        <header class="admin-header navbar fixed-top px-2 px-md-3">
            <div class="container-fluid gap-2 px-0">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <button id="toggleSidebar" class="btn btn-sidebar-toggle" type="button" aria-label="{{ __('Show or hide the menu') }}" aria-controls="sidebar">
                        <i data-feather="menu" class="icon-sm"></i>
                    </button>
                    <a class="brand d-flex align-items-center gap-2" href="{{ url('dashboard') }}">
                        <img src="{{ asset('front/image/logo.png') }}" alt="RRCONS">
                        <span class="d-flex flex-column lh-1">
                            <span>RRCONS</span>
                            <small class="d-none d-sm-inline">{{ __('Admin Panel') }}</small>
                        </span>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light d-none d-md-inline-flex align-items-center gap-1" title="{{ __('Opens the public website in a new tab') }}">
                        <i data-feather="external-link" class="icon-xs"></i> {{ __('View website') }}
                    </a>

                    <!-- Bahasa panel admin -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button"
                            id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Admin panel language') }}">
                            <img src="https://flagcdn.com/w40/{{ $flag }}.png" width="20" alt="{{ $langLabel }}">
                            <span class="d-none d-sm-inline">{{ $langLabel }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="langDropdown">
                            <li><h6 class="dropdown-header">{{ __('Admin panel language') }}</h6></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('set.language', 'id') }}">
                                    <img src="https://flagcdn.com/w40/id.png" width="20" alt="{{ __('Indonesia') }}"> {{ __('Indonesia') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('set.language', 'en') }}">
                                    <img src="https://flagcdn.com/w40/us.png" width="20" alt="{{ __('English') }}"> {{ __('English') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Profil -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                            id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar" title="{{ $authUser->name }}">{{ mb_strtoupper(mb_substr($authUser->name, 0, 1)) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown" style="min-width: 220px;">
                            <li class="px-3 py-2">
                                <div class="fw-semibold text-truncate">{{ $authUser->name }}</div>
                                <div class="small text-muted text-truncate">{{ $authUser->email }}</div>
                                <span class="badge role-badge mt-1">{{ __(ucfirst((string) $authUser->role)) }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger fw-semibold d-flex align-items-center gap-2" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i data-feather="log-out" class="icon-sm"></i> {{ __('Logout') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <!-- Sidebar -->
        @include('back.layout.sidebar')
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Main Content -->
        <main id="mainContent" class="content">
            <div class="page-card">
                @yield('content')
            </div>
        </main>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

        <script>
            (function () {
                const body = document.body;
                const desktop = () => window.matchMedia('(min-width: 992px)').matches;

                // Sidebar: di layar besar bisa disembunyikan (diingat), di HP tampil sebagai panel geser
                try {
                    if (localStorage.getItem('rrcons-sidebar') === 'collapsed' && desktop()) {
                        body.classList.add('sidebar-collapsed');
                    }
                } catch (e) {}

                document.getElementById('toggleSidebar')?.addEventListener('click', function () {
                    if (desktop()) {
                        body.classList.toggle('sidebar-collapsed');
                        try { localStorage.setItem('rrcons-sidebar', body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'open'); } catch (e) {}
                    } else {
                        body.classList.toggle('sidebar-open');
                    }
                });

                document.getElementById('sidebarBackdrop')?.addEventListener('click', () => body.classList.remove('sidebar-open'));

                document.addEventListener('DOMContentLoaded', function () {
                    feather.replace();

                    // Tooltip penjelasan
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

                    // Pratinjau gambar/video yang dipilih sebelum disimpan
                    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
                        input.addEventListener('change', function () {
                            const target = document.getElementById(input.dataset.preview);
                            const file = input.files && input.files[0];
                            if (!target || !file) return;
                            const url = URL.createObjectURL(file);
                            target.innerHTML = file.type.startsWith('video/')
                                ? '<video src="' + url + '" controls muted></video>'
                                : '<img src="' + url + '" alt="">';
                            target.insertAdjacentHTML('beforeend', '<div class="small text-muted mt-1">' + @json(__('New file (not saved yet)')) + ': ' + file.name.replace(/[<>&"]/g, '') + '</div>');
                        });
                    });

                    // Setelah validasi gagal, buka lagi form yang tadi diisi
                    @if (old('_modal'))
                        const failedModal = document.getElementById(@json(old('_modal')));
                        if (failedModal) bootstrap.Modal.getOrCreateInstance(failedModal).show();
                    @endif
                });
            })();
        </script>

        <!-- JS Tambahan per-halaman (opsional) -->
        @stack('js')
    </body>
</html>
