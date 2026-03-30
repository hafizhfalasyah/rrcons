<!-- Create Category Modal -->
<div class="modal fade" id="create-category-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="create-category-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="create-category-modal-label">
                    <i data-feather="user-plus" class="me-2" style="width: 20px; height: 20px;"></i> Add Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form -->
            <form action="{{ route('blog.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold">Category Name</label>
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
                        <i data-feather="save" class="me-1"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
