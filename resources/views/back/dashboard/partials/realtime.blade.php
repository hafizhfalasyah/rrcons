{{-- Panel real-time, diperbarui otomatis tiap beberapa detik. Variabel: $realtime --}}
<div class="card shadow-sm border-0 rounded-4 mb-4" id="realtime-panel">
    <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><span class="live-dot me-2"></span>{{ __('Real-time') }}</span>
        <small class="text-white-50" id="realtime-updated">{{ __('Updated') }}: {{ now()->format('H:i:s') }}</small>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3 text-center border-end">
                <div class="display-4 fw-bold text-success" id="realtime-active">{{ $realtime['active_visitors'] }}</div>
                <div class="text-muted small">{{ __('visitors online now') }}</div>
                <div class="text-muted small" id="realtime-devices"></div>
                <hr>
                <div class="d-flex justify-content-around small">
                    <div><div class="fw-bold fs-5" id="realtime-today-views">{{ number_format($realtime['today']['views']) }}</div>{{ __('views today') }}</div>
                    <div><div class="fw-bold fs-5" id="realtime-today-visitors">{{ number_format($realtime['today']['visitors']) }}</div>{{ __('visitors today') }}</div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="small fw-semibold mb-1">{{ __('Page views in the last 30 minutes') }}</div>
                <div style="height: 140px;"><canvas id="realtime-chart"></canvas></div>
                <div class="small fw-semibold mt-3 mb-1">{{ __('Pages being viewed now') }}</div>
                <ul class="list-group list-group-flush small" id="realtime-pages"></ul>
            </div>
            <div class="col-md-4">
                <div class="small fw-semibold mb-1">{{ __('Latest activity') }}</div>
                <ul class="list-group list-group-flush small realtime-feed" id="realtime-recent"></ul>
            </div>
        </div>
    </div>
</div>
