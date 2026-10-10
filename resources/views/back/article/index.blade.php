@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('List Articles'))

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')

        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h1 class="h3 fw-bold text-uppercase">{{ __('List Articles') }}</h1>
            <a href="{{ route('blog.articles.create') }}" class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center">
                <i data-feather="plus" class="icon-sm"></i> {{ __('Add Article') }}
            </a>
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

        <div class="mb-3">
            <p class="fw-semibold small mb-2">{{ __('Status explanation') }}</p>
            @include('back.article.partials.status_legend')
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-hover table-bordered align-middle shadow-sm" id="dataTable">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">{{ __('No') }}</th>
                        <th class="text-center">{{ __('Title') }}</th>
                        <th class="text-center">{{ __('Category') }}</th>
                        <th class="text-center">{{ __('Views') }}</th>
                        <th class="text-center">{{ __('Created By') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Publish Date') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

@endsection

@php
    $indexI18n = [
    'deleteTitle' => __('Delete article?'),
    'deleteText' => __('":title" will be permanently deleted, including its image. This cannot be undone.'),
    'deleteConfirm' => __('Yes, delete'),
    'cancel' => __('Cancel'),
    'deleted' => __('Deleted!'),
    'failed' => __('Failed to delete the article. Please try again.'),
    'language' => app()->getLocale() === 'id' ? [
        'search' => 'Cari:',
        'lengthMenu' => 'Tampilkan _MENU_ data',
        'info' => 'Menampilkan _START_–_END_ dari _TOTAL_ artikel',
        'infoEmpty' => 'Tidak ada artikel',
        'infoFiltered' => '(disaring dari _MAX_ artikel)',
        'zeroRecords' => 'Artikel tidak ditemukan',
        'emptyTable' => 'Belum ada artikel',
        'processing' => 'Memuat...',
        'paginate' => ['first' => 'Pertama', 'last' => 'Terakhir', 'next' => 'Berikutnya', 'previous' => 'Sebelumnya'],
    ] : [],
    ];
@endphp

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>

    <script>
        const i18n = @json($indexI18n);

        function deleteArticle(e) {
            const id = e.getAttribute('data-id');
            const title = e.getAttribute('data-title');

            Swal.fire({
                title: i18n.deleteTitle,
                text: i18n.deleteText.replace(':title', title),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: i18n.deleteConfirm,
                cancelButtonText: i18n.cancel,
                reverseButtons: true,
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const url = "{{ route('blog.articles.destroy', ':id') }}".replace(':id', id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'DELETE',
                    url: url,
                    success: function (response) {
                        Swal.fire({ title: i18n.deleted, text: response.message, icon: 'success' })
                            .then(() => $('#dataTable').DataTable().ajax.reload(null, false));
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', text: i18n.failed });
                    }
                });
            });
        }

        $(document).ready(function () {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url()->current() }}',
                language: i18n.language,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false, render: data => data + '.' },
                    { data: 'title', name: 'title_id', className: 'text-start' },
                    { data: 'category_id', name: 'category_id', className: 'text-start' },
                    { data: 'views', name: 'views', className: 'text-start' },
                    { data: 'created_by', name: 'created_by', className: 'text-start' },
                    { data: 'status', name: 'status', className: 'text-start' },
                    { data: 'publish_date', name: 'publish_date', className: 'text-start' },
                    { data: 'button', name: 'button', className: 'text-start', orderable: false, searchable: false },
                ],
                drawCallback: function () {
                    feather.replace();
                }
            });
        });
    </script>
@endpush
