@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Dashboard'))

@php
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
        'views' => ['label' => __('Page views'), 'icon' => 'bi-eye', 'format' => fn ($v) => number_format($v)],
        'visitors' => ['label' => __('Unique visitors'), 'icon' => 'bi-people', 'format' => fn ($v) => number_format($v)],
        'avg_engaged_seconds' => ['label' => __('Avg. reading time'), 'icon' => 'bi-stopwatch', 'format' => $duration],
        'pages_per_visitor' => ['label' => __('Pages per visitor'), 'icon' => 'bi-files', 'format' => fn ($v) => number_format($v, 2)],
        'single_page_rate' => ['label' => __('Single-page visits'), 'icon' => 'bi-box-arrow-right', 'format' => fn ($v) => $v . '%', 'inverse' => true],
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
@endphp

@push('css')
    <style>
        .live-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #20c997; box-shadow: 0 0 0 0 rgba(32, 201, 151, .7); animation: live-pulse 1.6s infinite; vertical-align: middle; }
        @keyframes live-pulse { 0% { box-shadow: 0 0 0 0 rgba(32, 201, 151, .7); } 70% { box-shadow: 0 0 0 10px rgba(32, 201, 151, 0); } 100% { box-shadow: 0 0 0 0 rgba(32, 201, 151, 0); } }
        .realtime-feed { max-height: 330px; overflow-y: auto; }
        .kpi-change { font-size: .8rem; }
    </style>
@endpush

@section('content')

    <!-- Heading -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">{{ __('Dashboard') }}</h1>

        <div class="text-end">
            <span class="d-block fw-semibold">
                {{ __('Hello, :name', ['name' => auth()->user()->name]) }} 👋
            </span>
            <small class="text-muted text-capitalize">
                {!! __('You are logged in as :role', ['role' => '<strong>' . e(__(ucfirst((string) auth()->user()->role))) . '</strong>']) !!}
            </small>
        </div>
    </div>

    <!-- Cards -->
    <div class="row g-4 mb-4">
        @foreach ([
            ['label' => __('Total Users'), 'value' => $total_users, 'icon' => 'bi-people-fill', 'class' => 'bg-gradient-success'],
            ['label' => __('Total Galleries'), 'value' => $total_galleries, 'icon' => 'bi-images', 'class' => 'bg-gradient-info'],
            ['label' => __('Total Articles'), 'value' => $total_articles, 'icon' => 'bi-file-earmark-text-fill', 'class' => 'bg-gradient-primary'],
            ['label' => __('Total Categories'), 'value' => $total_categories, 'icon' => 'bi-tags-fill', 'class' => 'bg-gradient-secondary'],
        ] as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card glass shadow-sm border-0 text-white {{ $card['class'] }} p-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-2">{{ $card['label'] }}</h5>
                            <h2 class="fw-bold">{{ $card['value'] }}</h2>
                        </div>
                        <i class="bi {{ $card['icon'] }} display-5 opacity-75"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ============================== ANALYTICS ============================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 mt-5" id="analytics">
        <h2 class="h5 fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2"></i>{{ __('Visitor analytics') }}</h2>
        <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Period') }}">
            @foreach (['today' => __('Today'), '7d' => __('7 days'), '30d' => __('30 days'), '90d' => __('90 days')] as $key => $label)
                <a href="{{ url('dashboard') }}?range={{ $key }}#analytics" class="btn {{ $report->range === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <p class="small text-muted">
        <i class="bi bi-shield-check me-1"></i>{{ __('Only real browsers are counted: bots, crawlers, scrapers without JavaScript and logged-in admins are excluded. No cookies are used and IP addresses are not stored.') }}
        @if ($legacyShare > 0)
            <br><i class="bi bi-info-circle me-1"></i>{{ __(':percent% of this period comes from the previous server-side counter (known bots filtered, at most one view per visitor per page per day, no source or reading time).', ['percent' => $legacyShare]) }}
        @endif
    </p>

    @include('back.dashboard.partials.realtime', ['realtime' => $realtime])

    <!-- KPI -->
    <div class="row g-3 mb-4">
        @foreach ($kpis as $key => $kpi)
            @php
                $item = $summary[$key];
                $good = $item['change'] === null ? null : (($kpi['inverse'] ?? false) ? $item['change'] <= 0 : $item['change'] >= 0);
            @endphp
            <div class="col-6 col-lg">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small"><i class="bi {{ $kpi['icon'] }} me-1"></i>{{ $kpi['label'] }}</div>
                        <div class="fs-3 fw-bold">{{ ($kpi['format'])($item['value']) }}</div>
                        @if ($item['change'] !== null)
                            <span class="kpi-change {{ $good ? 'text-success' : 'text-danger' }}" title="{{ __('Previous period') }}: {{ ($kpi['format'])($item['previous']) }}">
                                <i class="bi {{ $item['change'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                                {{ abs($item['change']) }}% {{ __('vs previous period') }}
                            </span>
                        @else
                            <span class="kpi-change text-muted">{{ __('No previous data') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tren -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-activity me-2 text-primary"></i>{{ __('Trend') }}</div>
        <div class="card-body">
            <div style="height: 280px;"><canvas id="trend-chart"></canvas></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Halaman teratas -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-trophy me-2 text-primary"></i>{{ __('Top pages') }}</div>
                <div class="card-body p-0">
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
                                    <tr><td colspan="4" class="text-muted text-center py-3">{{ __('No data yet for this period.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sumber kunjungan -->
        <div class="col-lg-5">
            @include('back.dashboard.partials.breakdown', ['title' => __('Traffic sources'), 'icon' => 'bi-signpost-split', 'rows' => $sources, 'labels' => $sourceLabels])
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Artikel terpopuler pada periode ini -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-journal-text me-2 text-primary"></i>{{ __('Most read articles in this period') }}</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Article') }}</th>
                                <th class="text-end">{{ __('Views') }}</th>
                                <th class="text-end">{{ __('Visitors') }}</th>
                                <th class="text-end">{{ __('Avg. reading time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topArticles as $row)
                                <tr>
                                    <td>
                                        @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                            <a href="{{ route('blog.articles.show', $row->article->id) }}">{{ $row->article->title }}</a>
                                        @else
                                            {{ $row->article->title }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($row->views) }}</td>
                                    <td class="text-end">{{ number_format($row->visitors) }}</td>
                                    <td class="text-end">{{ $duration($row->engaged) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">{{ __('No data yet for this period.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @include('back.dashboard.partials.breakdown', ['title' => __('Referring websites'), 'icon' => 'bi-link-45deg', 'rows' => $referrers])
        </div>
    </div>

    <div class="row g-4 mb-5">
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

    <!-- Latest & Popular Articles -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>{{ __('Latest Articles') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('No') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Publish Date') }}</th>
                                    @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latest_article as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->title }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->category?->name }}</span></td>
                                        <td>{{ optional($item->publish_date ?? $item->created_at)->translatedFormat('H:i | d F Y') }}</td>
                                        @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                            <td>
                                                <a href="{{ route('blog.articles.show', $item->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    {{ __('Detail') }}
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-fire me-2"></i>{{ __('Popular Articles') }}</span>
                    <small class="text-white-50">{{ __('all time') }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('No') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Views') }}</th>
                                    @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($popular_article as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->title }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->category?->name }}</span></td>
                                        <td><i class="bi bi-eye-fill me-1 text-muted"></i>{{ number_format($item->views) }}</td>
                                        @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                            <td>
                                                <a href="{{ route('blog.articles.show', $item->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    {{ __('Detail') }}
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const i18n = @json($dashboardI18n);
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // Tren harian
        new Chart(document.getElementById('trend-chart'), {
            type: 'line',
            data: {
                labels: i18n.daily.labels,
                datasets: [
                    { label: i18n.views, data: i18n.daily.views, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,.12)', fill: true, tension: .3 },
                    { label: i18n.visitors, data: i18n.daily.visitors, borderColor: '#20c997', backgroundColor: 'rgba(32,201,151,.1)', fill: true, tension: .3 },
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
            data: { labels: [], datasets: [{ label: i18n.views, data: [], backgroundColor: '#20c997', borderRadius: 3 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { ticks: { maxTicksLimit: 6 } } },
            },
        });

        function renderRealtime(data) {
            document.getElementById('realtime-active').textContent = data.active_visitors;
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

        // polling hanya saat tab dashboard terlihat
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
