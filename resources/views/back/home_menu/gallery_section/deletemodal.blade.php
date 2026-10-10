<!-- Delete Gallery Modal -->
@foreach ($gallery as $item)
    <div class="modal fade" id="delete-gallery-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-gallery-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-sm border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="delete-gallery-modal-label{{ $item->id }}">{{ __('Confirm Delete') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-6 mb-0">{{ __('Are you sure you want to delete gallery') }} <strong>{{ $item->title }}</strong>?</p>
                    <p class="text-muted">{{ __('This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <form action="{{ route('home.gallery.delete', $item->id) }}" method="post" class="d-inline">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
