{{-- Menampilkan teks dua bahasa di tabel. Props: id, en, limit --}}
@props(['id' => null, 'en' => null, 'limit' => 140])

<div class="lang-line">
    <span class="lang-tag">ID</span>
    <span>{{ filled($id) ? \Illuminate\Support\Str::limit($id, $limit) : '—' }}</span>
</div>
<div class="lang-line">
    <span class="lang-tag">EN</span>
    @if (filled($en))
        <span class="text-muted">{{ \Illuminate\Support\Str::limit($en, $limit) }}</span>
    @else
        <span class="small text-warning-emphasis fst-italic">{{ __('Not filled yet — the Indonesian version is shown.') }}</span>
    @endif
</div>
