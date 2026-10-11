{{-- Konfirmasi hapus. Props: id, action, itemName, message (opsional) --}}
@props(['id', 'action', 'itemName', 'message' => null])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger">
                <h5 class="modal-title d-flex align-items-center gap-2" id="{{ $id }}-label">
                    <i data-feather="trash-2" class="icon-sm"></i> {{ __('Confirm Delete') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-1">{{ $message ?? __('Are you sure you want to delete this item?') }}</p>
                <p class="fw-semibold mb-2">"{{ $itemName }}"</p>
                <p class="text-muted small mb-0">{{ __('This action cannot be undone.') }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
