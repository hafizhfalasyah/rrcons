<!-- Update Sosmed Section Modal -->
@foreach ($sosmed as $item)
    <div class="modal fade" id="update-sosmed-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-sosmed-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-sosmed-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Sosmed Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.sosmed.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Video Upload + Current Preview -->
                            <div class="col-12">
                                <label for="video-{{ $item->id }}" class="form-label fw-semibold">Video</label>
                                <input type="file" name="video" id="video-{{ $item->id }}" class="form-control @error('video') is-invalid @enderror" accept="video/mp4,video/mpeg,video/x-matroska,video/quicktime">
                                @error('video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($item->video)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">Current Video Preview:</label><br>
                                        <video controls class="rounded shadow-sm border" style="max-height: 200px; max-width: 100%;">
                                            <source src="{{ asset('storage/back/home/business/' . $item->video) }}" type="video/mp4">
                                            <source src="{{ asset('storage/back/home/business/' . $item->video) }}" type="video/mpeg">
                                            <source src="{{ asset('storage/back/home/business/' . $item->video) }}" type="video/x-matroska">
                                            <source src="{{ asset('storage/back/home/business/' . $item->video) }}" type="video/quicktime">
                                            Your browser does not support the video tag.
                                        </video>
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

                            <!-- Sosmed Link -->
                            <div class="col-12">
                                <label for="sosmed_link-{{ $item->id }}" class="form-label fw-semibold">Sosmed Link</label>
                                <input type="url" name="sosmed_link" id="sosmed_link-{{ $item->id }}" class="form-control @error('sosmed_link') is-invalid @enderror"
                                    value="{{ old('sosmed_link', $item->sosmed_link) }}" required>
                                @error('sosmed_link')
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
                            <i data-feather="save" class="me-1"></i> Update Sosmed Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
