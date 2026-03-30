<!-- Update Hero Section Modal -->
@foreach ($heroes as $item)
    <div class="modal fade" id="update-hero-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-hero-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-hero-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Hero Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.hero.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Image Upload + Preview -->
                            <div class="col-12">
                                <label for="bg_img-{{ $item->id }}" class="form-label fw-semibold">Background Image</label>
                                <input type="file" name="bg_img" id="bg_img-{{ $item->id }}" class="form-control @error('bg_img') is-invalid @enderror" accept="image/*">
                                @error('bg_img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($item->bg_img)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">Current Background Image Preview:</label><br>
                                        <img src="{{ asset('storage/back/home/hero/' . $item->bg_img) }}" alt="Current Image" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Hero Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
