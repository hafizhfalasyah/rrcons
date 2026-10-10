<!-- Create Poster Modal -->
<div class="modal fade" id="create-poster-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="create-poster-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="create-poster-modal-label">
                    <i data-feather="image" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Add poster') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <!-- Form -->
            <form action="{{ route('home.poster.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">

                        <!-- Image -->
                        <div class="col-12">
                            <label for="img" class="form-label fw-semibold">{{ __('Poster Image') }}</label>
                            <input type="file" name="img" id="img" class="form-control @error('img') is-invalid @enderror" accept="image/*">

                            @error('img')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <label for="name_id" class="form-label fw-semibold">{{ __('Poster Name (Indonesia)') }}</label>
                            <input type="text" name="name_id" id="name_id" class="form-control @error('name_id') is-invalid @enderror" value="{{ old('name_id') }}" required>

                            @error('name_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <label for="name_en" class="form-label fw-semibold">{{ __('Poster Name (English)') }}</label>
                            <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}">

                            @error('name_en')
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
                        <i data-feather="save" class="me-1"></i> {{ __('Add Poster') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
