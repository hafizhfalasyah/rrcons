<!-- Update About Section Modal -->
@foreach ($abouts as $item)
    <div class="modal fade" id="update-about-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-about-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-about-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit About Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.about.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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

                            <!-- Text as Textarea -->
                            <div class="col-12">
                                <label for="text_id-{{ $item->id }}" class="form-label fw-semibold">Text (Indonesia)</label>
                                <textarea name="text_id" id="text_id-{{ $item->id }}" class="form-control @error('text_id') is-invalid @enderror" rows="4" required>{{ old('text_id', $item->text_id) }}</textarea>
                                @error('text_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Text as Textarea -->
                            <div class="col-12">
                                <label for="text_en-{{ $item->id }}" class="form-label fw-semibold">Text (English)</label>
                                <textarea name="text_en" id="text_en-{{ $item->id }}" class="form-control @error('text_en') is-invalid @enderror" rows="4">{{ old('text_en', $item->text_en) }}</textarea>
                                @error('text_en')
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
                            <i data-feather="save" class="me-1"></i> Update About Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
