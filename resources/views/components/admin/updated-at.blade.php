{{-- Waktu terakhir diubah satu item. Props: value (Carbon|null) --}}
@props(['value' => null])

@if ($value)
    <span title="{{ $value->translatedFormat('d F Y H:i') }}" data-bs-toggle="tooltip">{{ $value->diffForHumans() }}</span>
    <div class="small text-muted">{{ $value->translatedFormat('d M Y') }}</div>
@else
    <span class="text-muted">—</span>
@endif
