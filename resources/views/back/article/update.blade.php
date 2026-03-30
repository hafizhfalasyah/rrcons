@extends('back.layout.template')

@section('title', 'RRCONS Admin | Edit Article')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 border-bottom pb-2">
        <h1 class="h3 fw-bold text-uppercase">Edit Article</h1>
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

    <div class="card shadow-sm mb-4 mt-4">
        <div class="card-body">
            <form action="{{ route('blog.articles.update', $article->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="oldImg" value="{{ $article->img }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $article->title) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select">
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $article->category_id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="desc" class="form-label">Description</label>
                    <textarea name="desc" id="editor" class="form-control" rows="10">{{ old('desc', $article->desc) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="img" class="form-label">Image (Max 2MB)</label>
                    <input type="file" name="img" id="img" class="form-control">
                    <div class="mt-2">
                        <small class="text-muted">Current Image:</small><br>
                        <img src="{{ asset('storage/back/blog/' . $article->img) }}" class="img-thumbnail img-preview" style="max-width: 300px;">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="0" {{ $article->status == 0 ? 'selected' : '' }}>Private</option>
                            <option value="1" {{ $article->status == 1 ? 'selected' : '' }}>Publish</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="publish_date" class="form-label">Publish Date</label>
                        <input type="date" name="publish_date" id="publish_date" class="form-control" value="{{ old('publish_date', $article->publish_date) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('blog.articles.index') }}" class="btn btn-danger btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                        <i data-feather="chevron-left" class="icon-sm"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                        <i data-feather="save" class="icon-sm"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>

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
    document.getElementById('img').addEventListener('change', function(event) {
        const preview = document.querySelector('.img-preview');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
