{{-- Tabel persentase. Variabel: $title, $icon, $rows, $labels (opsional: map nilai => label) --}}
<div class="card shadow-sm border-0 rounded-4 h-100">
    <div class="card-header bg-white fw-semibold"><i class="bi {{ $icon }} me-2 text-primary"></i>{{ $title }}</div>
    <div class="card-body">
        @forelse ($rows as $row)
            @php
                $label = $row->label === '__other' ? __('Others') : (($labels ?? [])[$row->label] ?? $row->label);
            @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between small">
                    <span class="text-truncate me-2" title="{{ $label }}">{{ $label }}</span>
                    <span class="text-muted text-nowrap">{{ number_format($row->views) }} · {{ $row->percent }}%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $row->percent }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">{{ __('No data yet for this period.') }}</p>
        @endforelse
    </div>
</div>
