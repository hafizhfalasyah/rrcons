<!-- Update Service Section Modal -->
@foreach ($services as $item)
    <div class="modal fade" id="update-service-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-service-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-service-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Service Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.service.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Name -->
                            <div class="col-12">
                                <label for="name-{{ $item->id }}" class="form-label fw-semibold">Name</label>
                                <input type="text" name="name" id="name-{{ $item->id }}" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $item->name) }}" required>
                                @error('name')
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

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Service Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
