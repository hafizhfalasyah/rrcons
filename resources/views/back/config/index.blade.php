@extends('back.layout.template')

@section('title', 'RRCONS Admin | List Configs')

@section('content')

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">List Configs</h1>
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
                    <th style="width: 20%;">Config Name</th>
                    <th style="width: 35%;">Value</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($config as $item => $key)
                    <tr>
                        <td class="text-center">{{ $config->firstItem() + $item }}.</td>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->value }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3" data-bs-toggle="modal"
                                    data-bs-target="#update-config-modal{{ $key->id }}">
                                    <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- <div>
            {{ $config->links() }}
        </div> --}}
    </div>

    @include('back.config.updatemodal')

@endsection
