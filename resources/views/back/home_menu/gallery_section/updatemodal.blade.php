<!-- Update Gallery Section Modal -->
@foreach ($gallery as $item)
    <div class="modal fade" id="update-gallery-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-gallery-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-gallery-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> Edit Gallery Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form action="{{ route('home.gallery.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">

                            <!-- Image Upload + Preview -->
                            <div class="col-12">
                                <label for="img-{{ $item->id }}" class="form-label fw-semibold">Image</label>
                                <input type="file" name="img" id="img-{{ $item->id }}" class="form-control @error('img') is-invalid @enderror" accept="image/*">
                                @error('img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($item->img)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">Current Image Preview:</label><br>
                                        <img src="{{ asset('storage/back/home/gallery/' . $item->img) }}" alt="Current Image" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title-{{ $item->id }}" class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" id="title-{{ $item->id }}" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $item->title) }}" required>
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

                            <!-- Month -->
                            <div class="col-md-6">
                                <label for="month" class="form-label fw-semibold">Month</label>
                                <select name="month" id="month" class="form-select @error('month') is-invalid @enderror" required>
                                    <option value="">-- Select Month --</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}"
                                            {{ old('month', $item->month ?? '') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Year -->
                            <div class="col-md-6">
                                <label for="year" class="form-label fw-semibold">Year</label>
                                <select name="year" id="year" class="form-select @error('year') is-invalid @enderror" required>
                                    <option value="">-- Select Year --</option>
                                    @for ($y = now()->year; $y >= 2000; $y--)
                                        <option value="{{ $y }}"
                                            {{ old('year', $item->year ?? '') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                @error('year')
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
                            <i data-feather="save" class="me-1"></i> Update Gallery Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
