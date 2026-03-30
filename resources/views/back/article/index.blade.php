@extends('back.layout.template')

@section('title', 'RRCONS Admin | List Articles')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')

        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h1 class="h3 fw-bold text-uppercase">List Articles</h1>
            <a href="{{ route('blog.articles.create') }}" class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center">
                <i data-feather="plus" class="icon-sm"></i> Add Article
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

        <div class="table-responsive mt-4">
            <table class="table table-hover table-bordered align-middle shadow-sm" id="dataTable">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Title</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Views</th>
                        <th class="text-center">Created By</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Publish Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const swal = $('.swal').data('swal');
        if (swal) {
            Swal.fire({
                title: 'Success',
                text: swal,
                icon: 'success',
                showConfirmButton: false,
                timer: 1000
            });
        }

        function deleteArticle(e) {
            let id = e.getAttribute('data-id');

            Swal.fire({
                title: 'Delete Article',
                text: "Are you sure?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = "{{ route('blog.articles.destroy', ':id') }}".replace(':id', id);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: url,
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                            }).then(() => {
                                window.location.href = "{{ route('blog.articles.index') }}";
                            });
                        },
                        error: function(xhr) {
                            alert(xhr.status + "\n" + xhr.responseText);
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', render: function (data, type, row, meta) {return data + '.';} },
                    { data: 'title', name: 'title', className: 'text-start' },
                    { data: 'category_id', name: 'category_id', className: 'text-start' },
                    { data: 'views', name: 'views', className: 'text-start' },
                    { data: 'created_by', name: 'created_by', className: 'text-start' },
                    { data: 'status', name: 'status', className: 'text-start' },
                    { data: 'publish_date', name: 'publish_date', className: 'text-start' },
                    { data: 'button', name: 'button', className: 'text-start' },
                ],
                drawCallback: function() {
                    feather.replace();
                }
            });
        });
    </script>
@endpush
