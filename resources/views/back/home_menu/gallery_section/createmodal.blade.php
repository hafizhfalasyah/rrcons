<!-- Create Gallery Modal -->
<div class="modal fade" id="create-gallery-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="create-gallery-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="create-gallery-modal-label">
                    <i data-feather="image" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Add Gallery') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <!-- Form -->
            <form action="{{ route('home.gallery.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">

                        <!-- Image -->
                        <div class="col-12">
                            <label for="img" class="form-label fw-semibold">{{ __('Image') }}</label>
                            <input type="file" name="img" id="img" class="form-control @error('img') is-invalid @enderror" accept="image/*">

                            @error('img')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="col-12">
                            <label for="title_id" class="form-label fw-semibold">{{ __('Title (Indonesia)') }}</label>
                            <input type="text" name="title_id" id="title_id" class="form-control @error('title_id') is-invalid @enderror" value="{{ old('title_id') }}" required>

                            @error('title_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="col-12">
                            <label for="title_en" class="form-label fw-semibold">{{ __('Title (English)') }}</label>
                            <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}">

                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Desc as Textarea -->
                        <div class="col-12">
                            <label for="desc_id" class="form-label fw-semibold">{{ __('Desc (Indonesia)') }}</label>
                            <textarea name="desc_id" id="desc_id" class="form-control @error('desc_id') is-invalid @enderror" rows="4">{{ old('desc_id') }}</textarea>

                            @error('desc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Desc as Textarea -->
                        <div class="col-12">
                            <label for="desc_en" class="form-label fw-semibold">{{ __('Desc (English)') }}</label>
                            <textarea name="desc_en" id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="4">{{ old('desc_en') }}</textarea>

                            @error('desc_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Month -->
                        <div class="col-md-6">
                            <label for="month" class="form-label fw-semibold">{{ __('Month') }}</label>
                            <select name="month" id="month" class="form-select @error('month') is-invalid @enderror" required>
                                <option value="">{{ __('-- Select Month --') }}</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
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
                            <label for="year" class="form-label fw-semibold">{{ __('Year') }}</label>
                            <select name="year" id="year" class="form-select @error('year') is-invalid @enderror" required>
                                <option value="">{{ __('-- Select Year --') }}</option>
                                @for ($y = now()->year; $y >= 2000; $y--)
                                    <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>
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
                        <i data-feather="x" class="me-1"></i> {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="me-1"></i> {{ __('Add Gallery') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
