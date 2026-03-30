@extends('back.layout.template')

@section('title', 'RRCONS Admin | List Users')

@section('content')

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">List Users</h1>
        @if (auth()->user()->isAdmin())
            <button class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#create-user-modal">
                <i data-feather="plus" class="icon-sm"></i> Add User
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
                    <th style="width: 20%;">User Name</th>
                    <th style="width: 25%;">Email Address</th>
                    <th style="width: 10%;">Role</th>
                    <th style="width: 20%;">Created At</th>
                    <th style="width: 30%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->role }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#update-user-modal{{ $item->id }}">
                                    <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                </button>

                                @if (auth()->user()->isAdmin() && $item->id != auth()->user()->id)
                                    <button class="btn btn-danger btn-sm d-flex align-items-center px-3" data-bs-toggle="modal"
                                        data-bs-target="#delete-user-modal{{ $item->id }}">
                                        <i data-feather="trash-2" class="icon-sm me-1"></i> Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('back.user.createmodal')
    @include('back.user.updatemodal')
    @include('back.user.deletemodal')

@endsection
