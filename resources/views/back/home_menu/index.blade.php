@extends('back.layout.template')

@section('title', 'RRCONS Admin | List Home Menus')

@section('content')

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h1 class="h3 fw-bold text-uppercase">Home Menus</h1>
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
                <th style="width: 20%;">Title</th>
                <th style="width: 35%;">Sub Title</th>
                <th style="width: 20%;">Last Updated</th>
                <th style="width: 20%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($homeMenus as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td>{{ $item->{'title_' . $lang} }}</td>
                    <td>{{ $item->{'sub_title_' . $lang} }}</td>
                    <td>{{ $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->translatedFormat('d F Y') : '-' }}</td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#update-homeMenu-modal{{ $item->id }}">
                            <i data-feather="edit" class="icon-sm me-1"></i> Edit
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('back.home_menu.updatemodal')

@endsection
