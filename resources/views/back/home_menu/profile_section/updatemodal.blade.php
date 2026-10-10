<!-- Update Profile Section Modal -->
@foreach ($profiles as $item)
    <div class="modal fade" id="update-profile-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-profile-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Edit Profile Section') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.profile.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Image Upload + Current Preview -->
                            <div class="col-12">
                                <label for="img-{{ $item->id }}" class="form-label fw-semibold">{{ __('Image') }}</label>
                                <input type="file" name="img" id="img-{{ $item->id }}" class="form-control @error('img') is-invalid @enderror" accept="image/*">
                                @error('img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($item->img)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">{{ __('Current Image Preview:') }}</label><br>
                                        <img src="{{ asset('storage/' . $item->img) }}" alt="{{ __('Current Image') }}" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>

                            <!-- Name -->
                            <div class="col-12">
                                <label for="name-{{ $item->id }}" class="form-label fw-semibold">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name-{{ $item->id }}" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div class="col-12">
                                <label for="position_id-{{ $item->id }}" class="form-label fw-semibold">{{ __('Position (Indonesia)') }}</label>
                                <input type="text" name="position_id" id="position_id-{{ $item->id }}" class="form-control @error('position_id') is-invalid @enderror" value="{{ old('position_id', $item->position_id) }}">
                                @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div class="col-12">
                                <label for="position_en-{{ $item->id }}" class="form-label fw-semibold">{{ __('Position (English)') }}</label>
                                <input type="text" name="position_en" id="position_en-{{ $item->id }}" class="form-control @error('position_en') is-invalid @enderror" value="{{ old('position_en', $item->position_en) }}">
                                @error('position_en')
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
                            <i data-feather="save" class="me-1"></i> {{ __('Update Profile Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
