<div id="sidebar" class="sidebar">
    <div class="p-4">
        <ul class="nav flex-column">
            <!-- DASHBOARD MENU -->
            @if (auth()->user()->hasAnyRole(['admin', 'writer', 'uploader']))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                        <i data-feather="grid" class="me-2"></i> {{ __('Dashboard') }}
                    </a>
                </li>
            @endif

            <!-- USERS MENU -->
            @if (auth()->user()->hasAnyRole(['admin', 'writer', 'uploader']))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ url('users') }}">
                        <i data-feather="user" class="me-2"></i> {{ __('Users') }}
                    </a>
                </li>
            @endif

            <!-- HOME MENU -->
            @php
                $homeRoutes = [
                    'home.menu'      => '-- Menus --',
                    'home.poster'    => 'Poster',
                    'home.hero'      => 'Hero',
                    'home.about'     => 'About',
                    'home.service'   => 'Service',
                    'home.advantage' => 'Advantage',
                    'home.profile'   => 'Profile',
                    'home.business'  => 'Business',
                    'home.sosmed'    => 'Social Media',
                    'home.gallery'   => 'Gallery',
                ];

                $isHomeActive = isMenuActive(array_keys($homeRoutes));
            @endphp

            @if (auth()->user()->hasAnyRole(['admin', 'uploader']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isHomeActive ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        href="#homeSubmenu"
                        role="button"
                        aria-expanded="{{ $isHomeActive ? 'true' : 'false' }}"
                        aria-controls="homeSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="home" class="me-2"></i>
                            <span>{{ __('Home') }}</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isHomeActive ? 'rotate-180' : '' }}"></i>
                    </a>

                    <div class="collapse ps-4 {{ $isHomeActive ? 'show' : '' }}" id="homeSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($homeRoutes as $routeName => $label)
                                @php
                                    // Tentukan akses per route
                                    $canAccess = match ($routeName) {
                                        'home.poster', 'home.gallery' => auth()->user()->hasAnyRole(['admin', 'uploader']),
                                        default => auth()->user()->hasRole('admin'),
                                    };
                                @endphp

                                @if ($canAccess)
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                                            href="{{ route($routeName) }}">
                                            {{ __($label) }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <!-- RRFUTURE MENU -->
            @php
                $rrfutureRoutes = [
                    'rrfuture.menu'    => '-- Menus --',
                    'rrfuture.hero'    => 'Hero',
                    'rrfuture.product' => 'Product',
                ];

                $isRRfutureActive = isMenuActive(array_keys($rrfutureRoutes));
            @endphp
            @if (auth()->user()->hasAnyRole(['admin']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isRRfutureActive ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        href="#rrfutureSubmenu"
                        role="button"
                        aria-expanded="{{ $isRRfutureActive ? 'true' : 'false' }}"
                        aria-controls="rrfutureSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="trending-up" class="me-2"></i>
                            <span>{{ __('RR Future') }}</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isRRfutureActive ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $isRRfutureActive ? 'show' : '' }}" id="rrfutureSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($rrfutureRoutes as $routeName => $label)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                                    href="{{ route($routeName) }}">
                                        {{ __($label) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <!-- SERVICE MENU -->
            @php
                $serviceRoutes = [
                    'service.menu'       => '-- Menus --',
                    'service.hero'       => 'Hero',
                    'service.ourService' => 'Our Service',
                ];

                $isServiceActive = isMenuActive(array_keys($serviceRoutes));
            @endphp
            @if (auth()->user()->hasAnyRole(['admin']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isServiceActive ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        href="#serviceSubmenu"
                        role="button"
                        aria-expanded="{{ $isServiceActive ? 'true' : 'false' }}"
                        aria-controls="serviceSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="briefcase" class="me-2"></i>
                            <span>{{ __('Service') }}</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isServiceActive ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $isServiceActive ? 'show' : '' }}" id="serviceSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($serviceRoutes as $routeName => $label)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                                    href="{{ route($routeName) }}">
                                        {{ __($label) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <!-- BLOGS MENU -->
            {{-- @php
                $blogRoutes = ['articles', 'categories', 'configs'];
                $isBlogActive = isMenuActive($blogRoutes);
            @endphp
            @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isBlogActive ? 'active' : '' }}"
                    data-bs-toggle="collapse"
                    href="#blogSubmenu"
                    role="button"
                    aria-expanded="{{ $isBlogActive ? 'true' : 'false' }}"
                    aria-controls="blogSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="file-text" class="me-2"></i>
                            <span>Blog</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isBlogActive ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $isBlogActive ? 'show' : '' }}" id="blogSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($blogRoutes as $route)
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is($route) || Request::is($route . '/*') ? 'active' : '' }}"
                                    href="{{ url($route) }}">
                                        {{ Str::title($route) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif --}}

            @php
                $blogRoutes = [
                    'blog.articles.index'   => 'Articles',
                    'blog.categories.index' => 'Categories',
                    'blog.comments.index'   => 'Comments',
                    'blog.configs.index'    => 'Configs',
                ];

                // Configs hanya untuk admin
                if (!auth()->user()->isAdmin()) {
                    unset($blogRoutes['blog.configs.index']);
                }

                $isBlogActive = isMenuActive(array_keys($blogRoutes));

                // jumlah komentar yang menunggu persetujuan (badge di menu Comments)
                $pendingComments = auth()->user()->hasAnyRole(['admin', 'writer'])
                    ? \App\Models\Blog\Comment::pending()->count()
                    : 0;
            @endphp
            @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isBlogActive ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        href="#blogSubmenu"
                        role="button"
                        aria-expanded="{{ $isBlogActive ? 'true' : 'false' }}"
                        aria-controls="blogSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="file-text" class="me-2"></i>
                            <span>{{ __('Blog') }}</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isBlogActive ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $isBlogActive ? 'show' : '' }}" id="blogSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($blogRoutes as $routeName => $label)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                                        href="{{ route($routeName) }}">
                                        {{ __($label) }}
                                        @if ($routeName === 'blog.comments.index' && $pendingComments > 0)
                                            <span class="badge rounded-pill bg-warning text-dark ms-1" title="{{ __('Waiting for approval') }}">{{ $pendingComments }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <!-- CONTACT MENU -->
            @php
                $contactRoutes = [
                    'contact.menu' => '-- Menus --',
                    'contact.hero' => 'Hero',
                    'contact.usContact' => 'Contact Us',
                ];

                $isContactActive = isMenuActive(array_keys($contactRoutes));
            @endphp
            @if (auth()->user()->hasAnyRole(['admin']))
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $isContactActive ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        href="#contactSubmenu"
                        role="button"
                        aria-expanded="{{ $isContactActive ? 'true' : 'false' }}"
                        aria-controls="contactSubmenu">
                        <div class="d-flex align-items-center">
                            <i data-feather="mail" class="me-2"></i>
                            <span>{{ __('Contact') }}</span>
                        </div>
                        <i data-feather="chevron-down" class="dropdown-icon {{ $isContactActive ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $isContactActive ? 'show' : '' }}" id="contactSubmenu">
                        <ul class="nav flex-column">
                            @foreach ($contactRoutes as $routeName => $label)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                                    href="{{ route($routeName) }}">
                                        {{ __($label) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <!-- LOGOUT MENU -->
            @if (auth()->user()->hasAnyRole(['admin', 'writer', 'uploader']))
                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <a class="nav-link logout-btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" class="me-2"></i> {{ __('Logout') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
