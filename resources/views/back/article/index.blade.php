@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Articles'))

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')

        <x-admin.page-header :title="__('Articles')" icon="file-text"
            :description="__('Blog articles. Each article can be written in Indonesian and English, and published now, scheduled, or kept private.')"
            :location="__('Blog page')" :preview-url="route('blog')">
            <x-slot:actions>
                <a href="{{ route('blog.articles.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                    <i data-feather="plus" class="icon-xs"></i> {{ __('Add Article') }}
                </a>
            </x-slot:actions>
            <x-slot:guide>
                <li>{{ __('Use the search box to find an article by title; click a column title to sort.') }}</li>
                <li>{{ __('The ID only label means the English version is still empty; English visitors then see the Indonesian text.') }}</li>
                <li>{{ __('Comments from visitors are checked in Blog → Comments before they appear.') }}</li>
            </x-slot:guide>
        </x-admin.page-header>

        <div class="mb-3">
            <p class="fw-semibold small mb-2">{{ __('Status explanation') }}</p>
            @include('back.article.partials.status_legend')
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-admin table-hover align-middle border" id="dataTable">
                <thead>
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
