<!-- Update Home Menu Modal -->
@foreach ($homeMenus as $item)
    <div class="modal fade" id="update-homeMenu-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-homeMenu-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-homeMenu-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Edit Home Menu') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.menu.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title_id-{{ $item->id }}" class="form-label fw-semibold">{{ __('Title (Indonesia)') }}</label>
                                <input type="text" name="title_id" id="title_id-{{ $item->id }}" class="form-control @error('title_id') is-invalid @enderror" value="{{ old('title_id', $item->title_id) }}" required>
                                @error('title_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title_en-{{ $item->id }}" class="form-label fw-semibold">{{ __('Title (English)') }}</label>
                                <input type="text" name="title_en" id="title_en-{{ $item->id }}" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $item->title_en) }}">
                                @error('title_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SubTitle as Textarea -->
                            <div class="col-12">
                                <label for="sub_title_id-{{ $item->id }}" class="form-label fw-semibold">{{ __('Sub Title (Indonesia)') }}</label>
                                <textarea name="sub_title_id" id="sub_title_id-{{ $item->id }}" class="form-control @error('sub_title_id') is-invalid @enderror" rows="4" required>{{ old('sub_title_id', $item->sub_title_id) }}</textarea>
                                @error('sub_title_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SubTitle as Textarea -->
                            <div class="col-12">
                                <label for="sub_title_en-{{ $item->id }}" class="form-label fw-semibold">{{ __('Sub Title (English)') }}</label>
                                <textarea name="sub_title_en" id="sub_title_en-{{ $item->id }}" class="form-control @error('sub_title_en') is-invalid @enderror" rows="4">{{ old('sub_title_en', $item->sub_title_en) }}</textarea>
                                @error('sub_title_en')
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
                            <i data-feather="save" class="me-1"></i> {{ __('Update Home Menu') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
