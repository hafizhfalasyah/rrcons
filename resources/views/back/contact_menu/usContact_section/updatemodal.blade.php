<!-- Update Contact Us Section Modal -->
@foreach ($us as $item)
    <div class="modal fade" id="update-usContact-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-usContact-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-usContact-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Edit Contact Us Section') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('contact.usContact.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title-{{ $item->id }}" class="form-label fw-semibold">{{ __('Title') }}</label>
                                <input type="text" id="title-{{ $item->id }}" class="form-control-plaintext fw-semibold" value="{{ $item->title }}" readonly>
                                <div class="form-text">{{ __('This name is used as a key on the Contact page and cannot be changed.') }}</div>
                            </div>

                            <!-- Desc as Textarea -->
                            <div class="col-12">
                                <label for="desc_id-{{ $item->id }}" class="form-label fw-semibold">{{ __('Desc (Indonesia)') }}</label>
                                <textarea name="desc_id" id="desc_id-{{ $item->id }}" class="form-control @error('desc_id') is-invalid @enderror" rows="4" required>{{ old('desc_id', $item->desc_id) }}</textarea>
                                @error('desc_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Desc as Textarea -->
                            <div class="col-12">
                                <label for="desc_en-{{ $item->id }}" class="form-label fw-semibold">{{ __('Desc (English)') }}</label>
                                <textarea name="desc_en" id="desc_en-{{ $item->id }}" class="form-control @error('desc_en') is-invalid @enderror" rows="4">{{ old('desc_en', $item->desc_en) }}</textarea>
                                @error('desc_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i data-feather="x" class="me-1"></i> {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> {{ __('Update Contact Us Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
