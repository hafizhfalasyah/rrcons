<!-- Update Business Section Modal -->
@foreach ($business as $item)
    <div class="modal fade" id="update-business-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-business-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-business-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Business Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.business.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Image Upload + Current Preview -->
                            <div class="col-12">
                                <label for="img-{{ $item->id }}" class="form-label fw-semibold">Image</label>
                                <input type="file" name="img" id="img-{{ $item->id }}" class="form-control @error('img') is-invalid @enderror" accept="image/*">
                                @error('img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($item->img)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">Current Image Preview:</label><br>
                                        <img src="{{ asset('storage/' . $item->img) }}" alt="Current Image" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title-{{ $item->id }}" class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" id="title-{{ $item->id }}" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $item->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Desc as Textarea -->
                            <div class="col-12">
                                <label for="desc-{{ $item->id }}" class="form-label fw-semibold">Desc</label>
                                <textarea name="desc" id="desc-{{ $item->id }}" class="form-control @error('desc') is-invalid @enderror" rows="4" required>{{ old('desc', $item->desc) }}</textarea>
                                @error('desc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Link -->
                            <div class="col-12">
                                <label for="form_link-{{ $item->id }}" class="form-label fw-semibold">Form Link</label>
                                <input type="url" name="form_link" id="form_link-{{ $item->id }}" class="form-control @error('form_link') is-invalid @enderror"
                                    value="{{ old('form_link', $item->form_link) }}" required>
                                @error('form_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Business Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
