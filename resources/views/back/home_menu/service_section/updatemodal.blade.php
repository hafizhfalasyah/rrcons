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
                                <label for="name_id-{{ $item->id }}" class="form-label fw-semibold">Name (Indonesia)</label>
                                <input type="text" name="name_id" id="name_id-{{ $item->id }}" class="form-control @error('name_id') is-invalid @enderror"
                                    value="{{ old('name_id', $item->name_id) }}" required>
                                @error('name_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="col-12">
                                <label for="name_en-{{ $item->id }}" class="form-label fw-semibold">Name (English)</label>
                                <input type="text" name="name_en" id="name_en-{{ $item->id }}" class="form-control @error('name_en') is-invalid @enderror"
                                    value="{{ old('name_en', $item->name_en) }}">
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Desc as Textarea -->
                            <div class="col-12">
                                <label for="desc_id-{{ $item->id }}" class="form-label fw-semibold">Desc (Indonesia)</label>
                                <textarea name="desc_id" id="desc_id-{{ $item->id }}" class="form-control @error('desc_id') is-invalid @enderror" rows="4" required>{{ old('desc_id', $item->desc_id) }}</textarea>
                                @error('desc_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Desc as Textarea -->
                            <div class="col-12">
                                <label for="desc_en-{{ $item->id }}" class="form-label fw-semibold">Desc (English)</label>
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
