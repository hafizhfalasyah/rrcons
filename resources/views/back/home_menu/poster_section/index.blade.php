@extends('back.layout.template')

@section('title', 'RRCONS Admin | Poster Section')

@section('content')

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">Poster Section</h1>
        @if (auth()->user()->isAdmin())
            <button class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#create-poster-modal">
                <i data-feather="plus" class="icon-sm"></i> Add Poster
            </button>
        @endif
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
                    <th style="width: 25%;">Image</th>
                    <th style="width: 15%;">Poster Name</th>
                    <th style="width: 10%;">Last Updated</th>
                    <th style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posters as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td class="text-center">
                            @if ($item->img)
                                <img src="{{ asset('storage/back/home/poster/' . $item->img) }}" alt="{{ $item->name }}" width="200">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#update-poster-modal{{ $item->id }}">
                                    <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                </button>

                                <button class="btn btn-danger btn-sm d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#delete-poster-modal{{ $item->id }}">
                                    <i data-feather="trash-2" class="icon-sm me-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('back.home_menu.poster_section.createmodal')
    @include('back.home_menu.poster_section.updatemodal')
    @include('back.home_menu.poster_section.deletemodal')

@endsection
