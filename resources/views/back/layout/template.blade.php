<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Deskripsi singkat halaman ini untuk keperluan SEO.">
        <meta name="author" content="Nama Penulis atau Perusahaan">

        <!-- CSRF Token for Laravel -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <!-- Feather Icon -->
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>

        {{-- <!-- Custom CSS (opsional jika digunakan) -->
        <link href="{{ asset('back/css/custom.css') }}" rel="stylesheet"> --}}

        <style>
            body {
                overflow-x: hidden;
                background-color: #f8f9fa;
            }

            .sidebar {
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden;
                transition: all 0.3s;
            }

            .sidebar.collapsed {
                width: 0;
                overflow: hidden;
            }

            .sidebar .nav-link {
                color: #0d6efd;
                font-weight: 500;
                border-radius: 0.375rem;
            }

            .sidebar .nav-link:hover,
            .sidebar .nav-link.active {
                background-color: #e7f1ff;
                color: #0a58ca;
            }

            .sidebar .nav-link.logout-btn {
                color: red;
                font-weight: 500;
                border-radius: 0.375rem;
            }

            .sidebar .nav-link.logout-btn:hover,
            .sidebar .nav-link.logout-btn.active {
                background-color: #ffe7e7;
                color: red;
            }

            .content {
                transition: margin-left 0.3s;
            }

            .content.full {
                margin-left: 0 !important;
            }

            /* Custom scrollbar untuk elemen tertentu */
            .sidebar::-webkit-scrollbar {
                width: 5px;
            }

            .sidebar::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .sidebar::-webkit-scrollbar-thumb {
                background: #e7f1ff; /* Warna utama scrollbar */
                border-radius: 10px;
            }

            @media (min-width: 992px) {
                .sidebar.collapsed ~ .content {
                margin-left: 0 !important;
                }

                .sidebar ~ .content {
                margin-left: 240px;
                }
            }

            .sidebar {
                width: 240px;
                background-color: #fff;
                border-right: 1px solid #dee2e6;
                position: fixed;
                top: 56px;
                left: 0;
                z-index: 1000;
            }

            .content {
                margin-top: 56px;
                padding: 2rem;
            }

            .navbar {
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .dropdown-item img {
                border-radius: 3px;
            }

            .navbar-brand {
                white-space: nowrap;
            }

            .dropdown-item img {
                border-radius: 3px;
            }

            .sidebar .nav-link {
                padding: 0.75rem 1rem;
                transition: background-color 0.2s ease;
            }

            .sidebar .dropdown-icon {
                transition: transform 0.3s ease;
            }

            .sidebar .nav-link[aria-expanded="true"] .dropdown-icon {
                transform: rotate(180deg);
            }

            .icon-sm {
                width: 18px;
                height: 18px;
                margin-right: 4px;
                stroke-width: 2;
            }

            .modal-content {
                animation: fadeInUp 0.3s ease;
            }

            @keyframes fadeInUp {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0px);
                    opacity: 1;
                }
            }

            .form-label {
                font-weight: 600;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #0d6efd, #2563eb);
            }

            .bg-gradient-secondary {
                background: linear-gradient(135deg, #6c757d, #495057);
            }

            .glass {
                backdrop-filter: blur(10px);
                background: rgba(13, 110, 253, 0.75);
                border-radius: 1rem;
            }

            .card-header i {
                font-size: 1.2rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.4em 0.6em;
                border-radius: 0.5rem;
            }
        </style>

        <!-- CSS Tambahan per-halaman (opsional) -->
        @stack('css')
    </head>

    <body>
        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
            <div class="container-fluid justify-content-between align-items-center">
                <!-- Kiri: Logo & Sidebar Toggle -->
                <div class="d-flex align-items-center">
                    <a class="navbar-brand fs-4 fw-bold ms-3" href="#">RRCONS</a>
                    <!-- Selalu tampil -->
                    <button id="toggleSidebar" class="btn btn-outline-light ms-5">☰</button>
                </div>

                <div class="d-flex align-items-center gap-3 me-3">
                    <!-- Kanan: Language Selector & Profile -->
                    @php
                        $currentLang = session('app_locale', 'id'); // Default ke 'id'
                        $flag = $currentLang === 'en' ? 'us' : 'id';
                        $label = $currentLang === 'en' ? 'English' : 'Indonesia';
                    @endphp

                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button"
                            id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://flagcdn.com/w40/{{ $flag }}.png" width="20" alt="{{ $label }}">
                            <span class="d-none d-sm-inline">{{ $label }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('set.language', 'id') }}">
                                    <img src="https://flagcdn.com/w40/id.png" width="20" alt="Indonesia"> Indonesia
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('set.language', 'en') }}">
                                    <img src="https://flagcdn.com/w40/us.png" width="20" alt="English"> English
                                </a>
                            </li>
                        </ul>
                    </div>


                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://i.pravatar.cc/40" alt="profile" width="36" height="36" class="rounded-circle">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#">My Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        @include('back.layout.sidebar')

        <!-- Main Content -->
        <main id="mainContent" class="content">
            <div class="card shadow-sm border-0">
                <div class="card-body px-5">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

        <!-- Collapse Sidebar JS -->
        <script>
            document.getElementById('toggleSidebar')?.addEventListener('click', function () {
                document.getElementById('sidebar')?.classList.toggle('collapsed');
            });
        </script>

        <!-- Feather Icon -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                feather.replace();
            });
        </script>

        <!-- Chart JS -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script>

        <!-- Custom JS (opsional jika digunakan) -->
        <script src="{{ asset('back/js/dashboard.js') }}"></script>

        <!-- JS Tambahan per-halaman (opsional) -->
        @stack('js')
    </body>
</html>
