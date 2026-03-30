@extends('back.layout.template')

@section('title', 'RRCONS Admin | Create Article')

@section('content')
<div class="container py-4">
    <div class="mb-4 border-bottom pb-2">
        <h1 class="h3 fw-bold text-uppercase">Create Article</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('blog.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="col-md-6">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="" hidden>-- Choose --</option>
                    @foreach ($categories as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="desc" class="form-label">Description</label>
            <textarea name="desc" id="editor" class="form-control" rows="10">{{ old('desc') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="img" class="form-label">Image (Max 2MB)</label>
            <input class="form-control" type="file" id="img" name="img">
            <div class="mt-2">
                <img src="" class="img-thumbnail img-preview d-none" alt="Preview" width="100%">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="" selected disabled>-- Choose --</option>
                    <option value="0">Private</option>
                    <option value="1">Publish</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="publish_date" class="form-label">Publish Date</label>
                <input type="date" name="publish_date" id="publish_date" class="form-control">
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('blog.articles.index') }}" class="btn btn-danger btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                <i data-feather="chevron-left" class="icon-sm"></i> Back
            </a>
            <button type="submit" class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                <i data-feather="save" class="icon-sm"></i> Save
            </button>
        </div>
    </form>
</div>
@endsection

@push('js')
    {{-- CKEditor 5 CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    uploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}'
                }
            })
            .catch(error => {
                console.error(error);
            });

        // Image preview
        document.getElementById('img').addEventListener('change', function(e) {
            const preview = document.querySelector('.img-preview');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
