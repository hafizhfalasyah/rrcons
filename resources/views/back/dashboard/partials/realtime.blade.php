{{-- Panel real-time, diperbarui otomatis tiap 10 detik. Variabel: $realtime --}}
<div id="realtime-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <span class="small text-muted"><span class="live-dot me-2"></span>{{ __('Updates automatically every few seconds while this page is open.') }}</span>
        <small class="text-muted" id="realtime-updated">{{ __('Updated') }}: {{ now()->format('H:i:s') }}</small>
    </div>
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="kpi-card text-center">
                <div class="display-5 fw-bold text-success" id="realtime-active">{{ $realtime['active_visitors'] }}</div>
                <div class="kpi-label">{{ __('visitors online now') }}</div>
                <div class="kpi-help" id="realtime-devices"></div>
                <hr>
                <div class="d-flex justify-content-around small">
                    <div><div class="fw-bold fs-5" id="realtime-today-views">{{ number_format($realtime['today']['views']) }}</div>{{ __('views today') }}</div>
                    <div><div class="fw-bold fs-5" id="realtime-today-visitors">{{ number_format($realtime['today']['visitors']) }}</div>{{ __('visitors today') }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="small fw-semibold mb-1">{{ __('Page views in the last 30 minutes') }}</div>
            <div style="height: 150px;"><canvas id="realtime-chart"></canvas></div>
            <div class="small fw-semibold mt-3 mb-1">{{ __('Pages being viewed now') }}</div>
            <ul class="list-group list-group-flush small" id="realtime-pages"></ul>
        </div>
        <div class="col-lg-4">
            <div class="small fw-semibold mb-1">{{ __('Latest activity') }}</div>
            <ul class="list-group list-group-flush small realtime-feed" id="realtime-recent"></ul>
        </div>
    </div>
</div>
