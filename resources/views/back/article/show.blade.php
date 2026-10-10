@extends('back.layout.template')

@section('title', 'RRCONS Admin | Detail Article')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 border-bottom pb-2">
        <h1 class="h3 fw-bold text-uppercase">Detail Article</h1>
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
            <table class="table table-bordered">
                <tr>
                    <th class="bg-light" style="width: 200px;">Title</th>
                    <td>{{ $article->title }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Category</th>
                    <td>{{ $article->Category->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Description</th>
                    <td>{!! $article->desc !!}</td>
                </tr>
                <tr>
                    <th class="bg-light">Image</th>
                    <td>
                        <a href="{{ asset('storage/' . $article->img) }}" target="_blank">
                            <img src="{{ asset('storage/' . $article->img) }}" class="img-fluid rounded" style="max-width: 50%;">
                        </a>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Views</th>
                    <td>{{ $article->views }}x</td>
                </tr>
                <tr>
                    <th class="bg-light">Status</th>
                    <td>
                        @if ($article->publication_status === \App\Models\Blog\Article::PUBLICATION_PRIVATE)
                            <span class="badge bg-danger px-3 py-2">Private</span>
                        @elseif ($article->publication_status === \App\Models\Blog\Article::PUBLICATION_SCHEDULED)
                            <span class="badge bg-warning text-dark px-3 py-2">Scheduled</span>
                        @else
                            <span class="badge bg-primary px-3 py-2">Published</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Publish Date</th>
                    <td>{{ optional($article->publish_date)->translatedFormat('d F Y H:i') ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Writer</th>
                    <td>{{ $article->User->name }}</td>
                </tr>
            </table>

            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('blog.articles.index') }}" class="btn btn-danger btn-sm me-1 d-flex align-items-center px-3 py-2 rounded-3">
                    <i data-feather="chevron-left" class="icon-sm"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
