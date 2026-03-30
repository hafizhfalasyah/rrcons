<!-- Update Service Menu Modal -->
@foreach ($serviceMenus as $item)
    <div class="modal fade" id="update-serviceMenu-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-serviceMenu-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-serviceMenu-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Service Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('service.menu.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title-{{ $item->id }}" class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" id="title-{{ $item->id }}" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $item->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SubTitle as Textarea -->
                            <div class="col-12">
                                <label for="sub_title-{{ $item->id }}" class="form-label fw-semibold">Sub Title</label>
                                <textarea name="sub_title" id="sub_title-{{ $item->id }}" class="form-control @error('sub_title') is-invalid @enderror" rows="4" required>{{ old('sub_title', $item->sub_title) }}</textarea>
                                @error('sub_title')
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
                            <i data-feather="save" class="me-1"></i> Update Service Menu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
