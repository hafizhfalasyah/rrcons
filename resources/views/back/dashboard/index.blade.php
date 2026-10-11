@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Dashboard'))

@php
    $user = auth()->user();
    $canBlog = $user->hasAnyRole(['admin', 'writer']);
    $canGallery = $user->hasAnyRole(['admin', 'uploader']);

    $sourceLabels = [
        'direct' => __('Direct'),
        'search' => __('Search engines'),
        'ai' => __('AI assistants (ChatGPT, Perplexity, …)'),
        'social' => __('Social media'),
        'referral' => __('Other websites'),
        'campaign' => __('Campaigns (UTM)'),
    ];
    $deviceLabels = ['desktop' => __('Desktop'), 'mobile' => __('Mobile'), 'tablet' => __('Tablet')];
    $localeLabels = ['id' => 'Bahasa Indonesia', 'en' => 'English'];
    $duration = fn ($seconds) => $seconds ? sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60) : '—';
    $kpis = [
        'views' => ['label' => __('Page views'), 'icon' => 'bi-eye', 'format' => fn ($v) => number_format($v),
            'help' => __('How many times pages of the website were opened.')],
        'visitors' => ['label' => __('Unique visitors'), 'icon' => 'bi-people', 'format' => fn ($v) => number_format($v),
            'help' => __('Estimated number of different people who visited.')],
        'avg_engaged_seconds' => ['label' => __('Avg. reading time'), 'icon' => 'bi-stopwatch', 'format' => $duration,
            'help' => __('How long a visitor reads one page on average (minutes:seconds).')],
        'pages_per_visitor' => ['label' => __('Pages per visitor'), 'icon' => 'bi-files', 'format' => fn ($v) => number_format($v, 2),
            'help' => __('How many pages one visitor opens on average.')],
        'single_page_rate' => ['label' => __('Single-page visits'), 'icon' => 'bi-box-arrow-right', 'format' => fn ($v) => $v . '%', 'inverse' => true,
            'help' => __('Visitors who leave after one page. Lower is better.')],
    ];
    $dashboardI18n = [
        'views' => __('Page views'),
        'visitors' => __('Unique visitors'),
        'online' => __('online'),
        'noActive' => __('No one is on the website right now.'),
        'noRecent' => __('No activity yet.'),
        'updated' => __('Updated'),
        'sources' => $sourceLabels,
        'devices' => $deviceLabels,
        'realtimeUrl' => route('dashboard.realtime'),
        'daily' => $daily,
        'realtime' => $realtime,
    ];
    $ranges = ['today' => __('Today'), '7d' => __('7 days'), '30d' => __('30 days'), '90d' => __('90 days')];

    $stats = [];
    if ($canBlog) {
        $stats[] = ['label' => __('Published articles'), 'value' => $published_articles, 'hint' => __(':total articles in total', ['total' => $total_articles]),
            'icon' => 'bi-file-earmark-text', 'tone' => 'bg-info-soft text-primary', 'url' => route('blog.articles.index')];
        $stats[] = ['label' => __('Comments waiting'), 'value' => $pending_comments, 'hint' => $pending_comments ? __('Need your approval') : __('Nothing to check'),
            'icon' => 'bi-chat-left-dots', 'tone' => $pending_comments ? 'bg-warning-soft text-warning' : 'bg-success-soft text-success', 'url' => route('blog.comments.index')];
        $stats[] = ['label' => __('Categories'), 'value' => $total_categories, 'hint' => __('Blog topics'),
            'icon' => 'bi-tags', 'tone' => 'bg-purple-soft text-purple', 'url' => route('blog.categories.index')];
    }
    if ($canGallery) {
        $stats[] = ['label' => __('Gallery photos'), 'value' => $total_galleries, 'hint' => __('Event Gallery'),
            'icon' => 'bi-images', 'tone' => 'bg-success-soft text-success', 'url' => route('home.gallery')];
        if (!$canBlog) {
            $stats[] = ['label' => __('Active posters'), 'value' => $total_posters, 'hint' => __('Pop-up Poster'),
                'icon' => 'bi-window-stack', 'tone' => 'bg-info-soft text-primary', 'url' => route('home.poster')];
        }
    }
    if ($user->isAdmin()) {
        $stats[] = ['label' => __('Users'), 'value' => $total_users, 'hint' => __('Accounts that can log in'),
            'icon' => 'bi-person-badge', 'tone' => 'bg-info-soft text-primary', 'url' => url('users')];
    }
@endphp

@section('content')

    <!-- Sapaan & aksi cepat -->
    <div class="welcome-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="small opacity-75">{{ now()->translatedFormat('l, d F Y') }}</div>
                <h1 class="h4 fw-bold mb-1">{{ __('Hello, :name', ['name' => $user->name]) }} 👋</h1>
                <div class="small opacity-75">
                    {!! __('You are logged in as :role', ['role' => '<strong>' . e(__(ucfirst((string) $user->role))) . '</strong>']) !!}.
                    {{ __('Here is a summary of the website and its visitors.') }}
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($canBlog)
                    <a href="{{ route('blog.articles.create') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-pencil-square"></i> {{ __('Write article') }}
                    </a>
                    @if ($pending_comments)
                        <a href="{{ route('blog.comments.index') }}" class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-chat-left-dots"></i> {{ __('Check comments') }} <span class="badge bg-warning text-dark">{{ $pending_comments }}</span>
                        </a>
                    @endif
                @endif
                @if ($canGallery)
                    <a href="{{ route('home.gallery') }}" class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-images"></i> {{ __('Event Gallery') }}
                    </a>
                @endif
                <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('View website') }}
                </a>
            </div>
        </div>
    </div>

    <x-admin.alerts />

    <!-- Ringkasan konten -->
    @if ($stats)
        <h2 class="dash-section-title"><i class="bi bi-grid text-primary"></i>{{ __('Content summary') }}</h2>
        <p class="dash-section-desc">{{ __('Click a card to open the related menu.') }}</p>
        <div class="row g-3 mb-4">
            @foreach ($stats as $stat)
                <div class="col-sm-6 col-xl">
                    <a href="{{ $stat['url'] }}" class="stat-card">
                        <span class="stat-icon {{ $stat['tone'] }}"><i class="bi {{ $stat['icon'] }}"></i></span>
                        <span>
                            <span class="stat-value d-block">{{ number_format($stat['value']) }}</span>
                            <span class="stat-label d-block">{{ $stat['label'] }}</span>
                            <span class="stat-hint d-block">{{ $stat['hint'] }}</span>
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================== PENGUNJUNG ============================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mt-2" id="analytics">
        <div>
            <h2 class="dash-section-title mb-1"><i class="bi bi-graph-up-arrow text-primary"></i>{{ __('Website visitors') }}</h2>
            <p class="dash-section-desc mb-0">{{ __('Who visits the website, which pages they read and where they come from.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">{{ __('Period') }}:</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Period') }}">
                @foreach ($ranges as $key => $label)
                    <a href="{{ url('dashboard') }}?range={{ $key }}" data-range-link class="btn {{ $report->range === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs analytics-tabs mt-3" role="tablist">
        @foreach ([
            'overview' => ['bi-speedometer2', __('Overview')],
            'realtime' => ['bi-broadcast', __('Right now')],
            'content' => ['bi-trophy', __('Pages & articles')],
            'audience' => ['bi-signpost-split', __('Sources & devices')],
        ] as $tab => [$icon, $label])
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $tab }}-btn" data-bs-toggle="tab" data-bs-target="#tab-{{ $tab }}"
                    type="button" role="tab" aria-controls="tab-{{ $tab }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                    @if ($tab === 'realtime')
                        <span class="badge bg-success ms-1" id="realtime-tab-count">{{ $realtime['active_visitors'] }}</span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4 mb-4 bg-white">
        <!-- Ringkasan periode -->
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel" aria-labelledby="tab-overview-btn">
            <p class="small text-muted mb-3">
                {{ __('Period') }}: <strong>{{ $ranges[$report->range] }}</strong>
                ({{ $report->from->translatedFormat('d M Y') }} – {{ $report->to->translatedFormat('d M Y') }}).
                {{ __('The small percentage compares with the previous period of the same length.') }}
            </p>
            <div class="row g-3 mb-4">
                @foreach ($kpis as $key => $kpi)
                    @php
                        $item = $summary[$key];
                        $good = $item['change'] === null ? null : (($kpi['inverse'] ?? false) ? $item['change'] <= 0 : $item['change'] >= 0);
                    @endphp
                    <div class="col-6 col-lg">
                        <div class="kpi-card">
                            <div class="kpi-label"><i class="bi {{ $kpi['icon'] }} me-1"></i>{{ $kpi['label'] }}</div>
                            <div class="kpi-value">{{ ($kpi['format'])($item['value']) }}</div>
                            @if ($item['change'] !== null)
                                <div class="kpi-change {{ $good ? 'text-success' : 'text-danger' }}" title="{{ __('Previous period') }}: {{ ($kpi['format'])($item['previous']) }}">
                                    <i class="bi {{ $item['change'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                                    {{ abs($item['change']) }}% {{ __('vs previous period') }}
                                </div>
                            @else
                                <div class="kpi-change text-muted">{{ __('No previous data') }}</div>
                            @endif
                            <div class="kpi-help mt-1">{{ $kpi['help'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="panel">
                <div class="panel-header">
                    <span><i class="bi bi-activity me-2 text-primary"></i>{{ __('Visits per day') }}</span>
                    <span class="panel-desc">{{ __('Blue: page views · Green: unique visitors') }}</span>
                </div>
                <div class="panel-body">
                    <div style="height: 280px;"><canvas id="trend-chart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Real-time -->
        <div class="tab-pane fade" id="tab-realtime" role="tabpanel" aria-labelledby="tab-realtime-btn">
            @include('back.dashboard.partials.realtime', ['realtime' => $realtime])
        </div>

        <!-- Halaman & artikel teratas -->
        <div class="tab-pane fade" id="tab-content" role="tabpanel" aria-labelledby="tab-content-btn">
            <div class="row g-4">
                <div class="col-xl-7">
                    <div class="panel">
                        <div class="panel-header">
                            <span><i class="bi bi-trophy me-2 text-primary"></i>{{ __('Most visited pages') }}</span>
                            <span class="panel-desc">{{ $ranges[$report->range] }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Page') }}</th>
                                        <th class="text-end">{{ __('Views') }}</th>
                                        <th class="text-end">{{ __('Visitors') }}</th>
                                        <th class="text-end">{{ __('Avg. reading time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topPages as $page)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-truncate" style="max-width: 320px;" title="{{ $page->label }}">{{ $page->label }}</div>
                                                <div class="text-muted text-truncate" style="max-width: 320px;">{{ $page->path }}</div>
                                            </td>
                                            <td class="text-end">{{ number_format($page->views) }}</td>
                                            <td class="text-end">{{ number_format($page->visitors) }}</td>
                                            <td class="text-end">{{ $duration($page->engaged) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted text-center py-4">{{ __('No data yet for this period.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="panel">
                        <div class="panel-header">
                            <span><i class="bi bi-journal-text me-2 text-primary"></i>{{ __('Most read articles in this period') }}</span>
                        </div>
                        <ul class="list-group list-group-flush small">
                            @forelse ($topArticles as $row)
                                <li class="list-group-item d-flex justify-content-between align-items-start gap-2 px-3">
                                    <span class="min-w-0">
                                        @if ($canBlog)
                                            <a href="{{ route('blog.articles.show', $row->article->id) }}" class="fw-semibold text-decoration-none">{{ $row->article->title }}</a>
                                        @else
                                            <span class="fw-semibold">{{ $row->article->title }}</span>
                                        @endif
                                        <span class="d-block text-muted">{{ __('Avg. reading time') }}: {{ $duration($row->engaged) }}</span>
                                    </span>
                                    <span class="text-nowrap text-end">
                                        <span class="fw-semibold d-block">{{ number_format($row->views) }}</span>
                                        <span class="text-muted">{{ __('views') }}</span>
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">{{ __('No data yet for this period.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sumber & perangkat -->
        <div class="tab-pane fade" id="tab-audience" role="tabpanel" aria-labelledby="tab-audience-btn">
            <div class="row g-4">
                <div class="col-lg-6">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Where visitors come from'), 'icon' => 'bi-signpost-split', 'rows' => $sources, 'labels' => $sourceLabels,
                        'description' => __('Direct means the address was typed or opened from a bookmark/WhatsApp.')])
                </div>
                <div class="col-lg-6">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Referring websites'), 'icon' => 'bi-link-45deg', 'rows' => $referrers,
                        'description' => __('Websites that link to RRCONS.')])
                </div>
                <div class="col-md-6 col-xl-3">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Devices'), 'icon' => 'bi-phone', 'rows' => $devices, 'labels' => $deviceLabels])
                </div>
                <div class="col-md-6 col-xl-3">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Browsers'), 'icon' => 'bi-browser-chrome', 'rows' => $browsers])
                </div>
                <div class="col-md-6 col-xl-3">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Operating systems'), 'icon' => 'bi-windows', 'rows' => $systems])
                </div>
                <div class="col-md-6 col-xl-3">
                    @include('back.dashboard.partials.breakdown', ['title' => __('Website language'), 'icon' => 'bi-translate', 'rows' => $languages, 'labels' => $localeLabels])
                </div>
                @if ($countries->isNotEmpty())
                    <div class="col-md-6 col-xl-3">
                        @include('back.dashboard.partials.breakdown', ['title' => __('Countries'), 'icon' => 'bi-globe2', 'rows' => $countries])
                    </div>
                @endif
            </div>
        </div>

        <p class="small text-muted mt-4 mb-0">
            <i class="bi bi-shield-check me-1"></i>{{ __('Only real browsers are counted: bots, crawlers, scrapers without JavaScript and logged-in admins are excluded. No cookies are used and IP addresses are not stored.') }}
            @if ($legacyShare > 0)
                <br><i class="bi bi-info-circle me-1"></i>{{ __(':percent% of this period comes from the previous server-side counter (known bots filtered, at most one view per visitor per page per day, no source or reading time).', ['percent' => $legacyShare]) }}
            @endif
        </p>
    </div>

    <!-- Artikel -->
    <h2 class="dash-section-title"><i class="bi bi-journals text-primary"></i>{{ __('Articles') }}</h2>
    <p class="dash-section-desc">{{ __('The newest published articles and the most read articles of all time.') }}</p>
    <div class="row g-4">
        @foreach ([
            ['title' => __('Latest Articles'), 'icon' => 'bi-clock-history', 'items' => $latest_article->take(5), 'meta' => fn ($item) => optional($item->publish_date ?? $item->created_at)->translatedFormat('d M Y, H:i')],
            ['title' => __('Popular Articles'), 'icon' => 'bi-fire', 'items' => $popular_article, 'meta' => fn ($item) => number_format($item->views) . ' ' . __('views'), 'badge' => __('all time')],
        ] as $list)
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-header">
                        <span><i class="bi {{ $list['icon'] }} me-2 text-primary"></i>{{ $list['title'] }}</span>
                        @isset($list['badge'])<span class="panel-desc">{{ $list['badge'] }}</span>@endisset
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($list['items'] as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2 px-3">
                                <span class="min-w-0">
                                    <span class="fw-semibold d-block text-truncate" title="{{ $item->title }}">{{ $item->title }}</span>
                                    <span class="small text-muted">
                                        @if ($item->category)<span class="badge bg-light text-dark border me-1">{{ $item->category->name }}</span>@endif
                                        {{ ($list['meta'])($item) }}
                                    </span>
                                </span>
                                @if ($canBlog)
                                    <a href="{{ route('blog.articles.show', $item->id) }}" class="btn btn-sm btn-outline-primary flex-shrink-0">{{ __('Detail') }}</a>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-4">{{ __('No articles yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const i18n = @json($dashboardI18n);
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // Tab yang terakhir dibuka tetap terbuka setelah ganti periode
        const tabFromHash = document.getElementById((location.hash || '').replace('#', '') + '-btn');
        if (tabFromHash) bootstrap.Tab.getOrCreateInstance(tabFromHash).show();
        document.querySelectorAll('.analytics-tabs [data-bs-toggle="tab"]').forEach(button => {
            button.addEventListener('shown.bs.tab', () => history.replaceState(null, '', button.dataset.bsTarget));
        });
        document.querySelectorAll('[data-range-link]').forEach(link => {
            link.addEventListener('click', () => {
                const active = document.querySelector('.analytics-tabs .nav-link.active');
                if (active) link.href = link.href.split('#')[0] + active.dataset.bsTarget;
            });
        });

        // Tren harian
        new Chart(document.getElementById('trend-chart'), {
            type: 'line',
            data: {
                labels: i18n.daily.labels,
                datasets: [
                    { label: i18n.views, data: i18n.daily.views, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.12)', fill: true, tension: .3 },
                    { label: i18n.visitors, data: i18n.daily.visitors, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)', fill: true, tension: .3 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        // Real-time
        const minuteChart = new Chart(document.getElementById('realtime-chart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: i18n.views, data: [], backgroundColor: '#10b981', borderRadius: 3 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { ticks: { maxTicksLimit: 6 } } },
            },
        });

        function renderRealtime(data) {
            document.getElementById('realtime-active').textContent = data.active_visitors;
            document.getElementById('realtime-tab-count').textContent = data.active_visitors;
            document.getElementById('realtime-today-views').textContent = Number(data.today.views).toLocaleString();
            document.getElementById('realtime-today-visitors').textContent = Number(data.today.visitors).toLocaleString();
            document.getElementById('realtime-updated').textContent = i18n.updated + ': ' + new Date(data.generated_at).toLocaleTimeString();
            document.getElementById('realtime-devices').textContent = Object.entries(data.devices || {})
                .map(([device, count]) => (i18n.devices[device] || device) + ' ' + count).join(' · ');

            minuteChart.data.labels = data.per_minute.map(m => m.label);
            minuteChart.data.datasets[0].data = data.per_minute.map(m => m.views);
            minuteChart.update('none');

            document.getElementById('realtime-pages').innerHTML = data.active_pages.length
                ? data.active_pages.map(p => `<li class="list-group-item d-flex justify-content-between px-0"><span class="text-truncate me-2" title="${escapeHtml(p.path)}">${escapeHtml(p.label)}</span><span class="badge bg-success rounded-pill">${p.visitors}</span></li>`).join('')
                : `<li class="list-group-item px-0 text-muted">${escapeHtml(i18n.noActive)}</li>`;

            document.getElementById('realtime-recent').innerHTML = data.recent.length
                ? data.recent.map(r => `<li class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <span class="text-truncate me-2 fw-semibold" title="${escapeHtml(r.path)}">${escapeHtml(r.label)}</span>
                            <span class="text-muted text-nowrap">${escapeHtml(r.time)}</span>
                        </div>
                        <div class="text-muted">
                            ${r.online ? `<span class="badge bg-success me-1">${escapeHtml(i18n.online)}</span>` : ''}
                            ${escapeHtml(i18n.devices[r.device] || r.device || '')} · ${escapeHtml(r.browser || '')}
                            · ${escapeHtml(i18n.sources[r.source] || r.source || '')}${r.referrer ? ' (' + escapeHtml(r.referrer) + ')' : ''}
                            ${r.country ? ' · ' + escapeHtml(r.country) : ''}
                        </div>
                    </li>`).join('')
                : `<li class="list-group-item px-0 text-muted">${escapeHtml(i18n.noRecent)}</li>`;
        }

        renderRealtime(i18n.realtime);

        // polling hanya saat tab browser dashboard terlihat
        async function refresh() {
            if (document.visibilityState !== 'visible') return;
            try {
                const response = await fetch(i18n.realtimeUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                if (response.ok) renderRealtime(await response.json());
            } catch (e) { /* koneksi terputus sementara, coba lagi di siklus berikutnya */ }
        }

        setInterval(refresh, 10000);
        document.addEventListener('visibilitychange', refresh);
    })();
</script>
@endpush
