<!-- Create Poster Modal -->
<div class="modal fade" id="create-poster-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="create-poster-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="create-poster-modal-label">
                    <i data-feather="image" class="me-2" style="width: 20px; height: 20px;"></i> Add poster
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form -->
            <form action="{{ route('home.poster.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">

                        <!-- Image -->
                        <div class="col-12">
                            <label for="img" class="form-label fw-semibold">Poster Image</label>
                            <input type="file" name="img" id="img" class="form-control @error('img') is-invalid @enderror" accept="image/*">

                            @error('img')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold">Poster Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>

                            @error('name')
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
                        <i data-feather="save" class="me-1"></i> Add Poster
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
