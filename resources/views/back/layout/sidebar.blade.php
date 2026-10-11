{{--
    Menu admin. Hak akses per menu harus sejalan dengan middleware di routes/web.php
    (dan tests/Feature/AccessControlTest.php):
    - admin    : semua menu
    - writer   : dashboard, akun sendiri, blog
    - uploader : dashboard, akun sendiri, poster & galeri beranda
--}}
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();

    // Halaman website yang kontennya bisa diubah, beserta bagian-bagiannya
    $contentGroups = [
        'home' => [
            'label' => __('Home Page'),
            'icon' => 'home',
            'items' => [
                'home.menu' => [__('Section Headings'), ['admin']],
                'home.poster' => [__('Pop-up Poster'), ['admin', 'uploader']],
                'home.hero' => [__('Main Banner'), ['admin']],
                'home.about' => [__('About Us'), ['admin']],
                'home.service' => [__('Services'), ['admin']],
                'home.advantage' => [__('Advantages'), ['admin']],
                'home.profile' => [__('Team Profiles'), ['admin']],
                'home.business' => [__('Business Training'), ['admin']],
                'home.sosmed' => [__('Social Media Videos'), ['admin']],
                'home.gallery' => [__('Event Gallery'), ['admin', 'uploader']],
            ],
        ],
        'rrfuture' => [
            'label' => __('RR Future Tech Page'),
            'icon' => 'cpu',
            'items' => [
                'rrfuture.menu' => [__('Section Headings'), ['admin']],
                'rrfuture.hero' => [__('Main Banner'), ['admin']],
                'rrfuture.product' => [__('Products'), ['admin']],
            ],
        ],
        'service' => [
            'label' => __('RR Vision Page'),
            'icon' => 'briefcase',
            'items' => [
                'service.menu' => [__('Section Headings'), ['admin']],
                'service.hero' => [__('Main Banner'), ['admin']],
                'service.ourService' => [__('Services'), ['admin']],
            ],
        ],
        'contact' => [
            'label' => __('Contact Page'),
            'icon' => 'phone',
            'items' => [
                'contact.menu' => [__('Section Headings'), ['admin']],
                'contact.hero' => [__('Main Banner'), ['admin']],
                'contact.usContact' => [__('Contact Details'), ['admin']],
            ],
        ],
    ];

    foreach ($contentGroups as $key => $group) {
        $contentGroups[$key]['items'] = array_filter($group['items'], fn ($item) => $user->hasAnyRole($item[1]));
    }
    $contentGroups = array_filter($contentGroups, fn ($group) => $group['items'] !== []);

    $blogItems = [];
    if ($user->hasAnyRole(['admin', 'writer'])) {
        $blogItems = [
            'blog.articles.index' => [__('Articles'), 'file-text'],
            'blog.categories.index' => [__('Categories'), 'tag'],
            'blog.comments.index' => [__('Comments'), 'message-square'],
        ];
        if ($isAdmin) {
            $blogItems['blog.configs.index'] = [__('Blog Settings'), 'sliders'];
        }
    }

    // jumlah komentar yang menunggu persetujuan (badge di menu Comments)
    $pendingComments = $blogItems ? \App\Models\Blog\Comment::pending()->count() : 0;
@endphp

<aside id="sidebar" class="sidebar" aria-label="{{ __('Admin menu') }}">
    <div class="sidebar-scroll">
        <ul class="nav flex-column">
            <li class="sidebar-section">{{ __('Overview') }}</li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                    <i data-feather="grid"></i> {{ __('Dashboard') }}
                </a>
            </li>
        </ul>

        @if ($contentGroups)
            <ul class="nav flex-column" id="sidebarContentGroups">
                <li class="sidebar-section">{{ __('Website content') }}</li>
                @foreach ($contentGroups as $key => $group)
                    @php $active = isMenuActive(array_keys($group['items'])); @endphp
                    <li class="nav-item">
                        <a class="nav-link nav-group-toggle {{ $active ? 'active' : '' }}" data-bs-toggle="collapse" href="#menu-{{ $key }}"
                            role="button" aria-expanded="{{ $active ? 'true' : 'false' }}" aria-controls="menu-{{ $key }}">
                            <i data-feather="{{ $group['icon'] }}"></i>
                            <span>{{ $group['label'] }}</span>
                            <i data-feather="chevron-down" class="chevron"></i>
                        </a>
                        <div class="collapse {{ $active ? 'show' : '' }}" id="menu-{{ $key }}" data-bs-parent="#sidebarContentGroups">
                            <ul class="nav flex-column submenu">
                                @foreach ($group['items'] as $routeName => [$label])
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}" href="{{ route($routeName) }}">{{ $label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($blogItems)
            <ul class="nav flex-column">
                <li class="sidebar-section">{{ __('Blog') }}</li>
                @foreach ($blogItems as $routeName => [$label, $icon])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(str_replace('.index', '.*', $routeName)) ? 'active' : '' }}" href="{{ route($routeName) }}">
                            <i data-feather="{{ $icon }}"></i> <span>{{ $label }}</span>
                            @if ($routeName === 'blog.comments.index' && $pendingComments > 0)
                                <span class="badge rounded-pill bg-warning text-dark ms-auto" title="{{ __('Waiting for approval') }}">{{ $pendingComments }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <ul class="nav flex-column">
            <li class="sidebar-section">{{ __('Account') }}</li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ url('users') }}">
                    <i data-feather="{{ $isAdmin ? 'users' : 'user' }}"></i> {{ $isAdmin ? __('Users') : __('My Account') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Selalu terlihat walaupun menu di atas dibuka semua -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="avatar border" style="width: 32px; height: 32px;">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <div class="user-name text-truncate">{{ $user->name }}</div>
                <div class="small text-muted">{{ __(ucfirst((string) $user->role)) }}</div>
            </div>
        </div>
        <a class="btn btn-logout btn-sm w-100 d-flex align-items-center justify-content-center gap-2" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i data-feather="log-out" class="icon-sm"></i> {{ __('Logout') }}
        </a>
    </div>
</aside>
