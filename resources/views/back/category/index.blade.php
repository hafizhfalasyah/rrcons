@extends('back.layout.template')

@section('title', 'RRCONS Admin | List Categories')

@section('content')

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">List Categories</h1>
        <button class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#create-category-modal">
            <i data-feather="plus" class="icon-sm"></i> Add Category
        </button>
    </div>

    {{-- Flash & Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive mt-4">
        <table class="table table-hover table-bordered align-middle shadow-sm">
            <thead class="table-primary text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Category Name</th>
                    <th style="width: 20%;">Slug</th>
                    <th style="width: 20%;">Created At</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->slug }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#update-category-modal{{ $item->id }}">
                                    <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#delete-category-modal{{ $item->id }}">
                                    <i data-feather="trash-2" class="icon-sm me-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('back.category.createmodal')
    @include('back.category.updatemodal')
    @include('back.category.deletemodal')

@endsection
