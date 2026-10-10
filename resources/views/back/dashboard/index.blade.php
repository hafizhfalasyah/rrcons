@extends('back.layout.template')

@section('title', 'RRCONS Admin | Dashboard')

@section('content')

    <!-- Heading -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">Dashboard</h1>

        <div class="text-end">
            <span class="d-block fw-semibold">
                Halo, {{ auth()->user()->name }} 👋
            </span>
            <small class="text-muted text-capitalize">
                You are logged in as <strong>{{ auth()->user()->role }}</strong>
            </small>
        </div>
    </div>

    <!-- Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users Cards -->
        <div class="col-md-6 col-xl-3">
            <div class="card glass shadow-sm border-0 text-white bg-gradient-success p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-2">Total Users</h5>
                        <h2 class="fw-bold">{{ $total_users }}</h2>
                    </div>
                    <i class="bi bi-people-fill display-5 opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Total Gallery Cards -->
        <div class="col-md-6 col-xl-3">
            <div class="card glass shadow-sm border-0 text-white bg-gradient-info p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-2">Total Galleries</h5>
                        <h2 class="fw-bold">0</h2>
                    </div>
                    <i class="bi bi-images display-5 opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Total Articles Cards -->
        <div class="col-md-6 col-xl-3">
            <div class="card glass shadow-sm border-0 text-white bg-gradient-primary p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-2">Total Articles</h5>
                        <h2 class="fw-bold">{{ $total_articles }}</h2>
                    </div>
                    <i class="bi bi-file-earmark-text-fill display-5 opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Total Categories Cards -->
        <div class="col-md-6 col-xl-3">
            <div class="card glass shadow-sm border-0 text-white bg-gradient-secondary p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-2">Total Categories</h5>
                        <h2 class="fw-bold">{{ $total_categories }}</h2>
                    </div>
                    <i class="bi bi-tags-fill display-5 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest & Popular Articles -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Latest Articles</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latest_article as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->title }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->category->name }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('H:i | d F Y') }}</td>
                                        @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                            <td>
                                                <a href="{{ route('blog.articles.show', $item->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Detail
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-fire me-2"></i>Popular Articles</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Views</th>
                                    @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($popular_article as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->title }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->category->name }}</span></td>
                                        <td><i class="bi bi-eye-fill me-1 text-muted"></i>{{ $item->views }}</td>
                                        @if (auth()->user()->hasAnyRole(['admin', 'writer']))
                                            <td>
                                                <a href="{{ route('blog.articles.show', $item->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Detail
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Statistics Chart -->
    <div class="mt-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2"></i>Visitor Statistics (Per Page - Last 7 Days)</span>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" height="120"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    const labels = {!! json_encode($labels) !!};
    const datasets = {!! json_encode($visitorStats) !!};

    // Warna biru dan variasi kontras tinggi (non-monoton)
    const colorPalette = [
        '#0d6efd', // biru utama
        '#6610f2', // ungu biru
        '#20c997', // toska
        '#fd7e14', // oranye
        '#198754', // hijau
        '#dc3545', // merah
        '#ffc107', // kuning
        '#6f42c1', // ungu
        '#0dcaf0', // cyan
        '#17a2b8'  // biru kehijauan
    ];

    const ctx = document.getElementById('visitorChart').getContext('2d');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets.map((ds, index) => {
                const total = ds.data.reduce((a, b) => a + b, 0); // hitung total pengunjung
                return {
                    label: `${ds.label} (${total})`, // contoh: Home (23)
                    data: ds.data,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    borderColor: colorPalette[index % colorPalette.length],
                    backgroundColor: colorPalette[index % colorPalette.length],
                    pointBackgroundColor: 'white',
                    pointBorderColor: colorPalette[index % colorPalette.length],
                    pointRadius: 5,
                    pointHoverRadius: 7
                };
            })
        },
        options: {
            responsive: true,
            layout: {
                padding: {
                    top: 20,
                    bottom: 20
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                datalabels: {
                    color: '#333',
                    anchor: 'end',
                    align: 'top',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: Math.round
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Pengunjung',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
@endpush


