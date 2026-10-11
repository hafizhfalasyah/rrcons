{{-- Pesan sukses & error validasi --}}
@if (session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2 shadow-sm" role="alert">
        <i data-feather="check-circle" class="icon-sm mt-1 flex-shrink-0"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger shadow-sm" role="alert">
        <div class="fw-semibold d-flex align-items-center gap-2 mb-1">
            <i data-feather="alert-triangle" class="icon-sm"></i> {{ __('The data has not been saved. Please check:') }}
        </div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
