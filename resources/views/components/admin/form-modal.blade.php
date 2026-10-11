{{--
    Modal berisi form (tambah/ubah). Field tersembunyi "_modal" membuat modal ini terbuka lagi
    otomatis bila validasi gagal (lihat layout/template.blade.php).
    Props : id, title, action, method (POST/PUT), submit (teks tombol), icon, size (lg/xl/''), files, intro
--}}
@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'submit' => null,
    'icon' => 'edit',
    'size' => 'lg',
    'files' => false,
    'intro' => null,
])

<div class="modal fade" id="{{ $id }}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="{{ $id }}-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $size ? 'modal-' . $size : '' }}">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="{{ $id }}-label">
                    <i data-feather="{{ $icon }}" class="icon-sm"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <form action="{{ $action }}" method="POST" @if ($files) enctype="multipart/form-data" @endif class="d-flex flex-column overflow-hidden">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <input type="hidden" name="_modal" value="{{ $id }}">

                <div class="modal-body px-4 py-3">
                    @if ($intro)
                        <p class="modal-intro"><i data-feather="info" class="icon-xs me-1"></i>{{ $intro }}</p>
                    @endif
                    {{ $slot }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i data-feather="save" class="icon-sm"></i> {{ $submit ?? __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
