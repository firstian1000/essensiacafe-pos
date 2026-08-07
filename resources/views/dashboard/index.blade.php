@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}?v=3">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('title','Dashboard')

@section('content')

<div class="dashboard-page">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1>Selamat datang kembali, Admin! 👋</h1>
            <p>Berikut ringkasan aktivitas operasional Essensia Koffie hari ini.</p>
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('dashboard.export', ['date' => $selectedDate]) }}" class="btn-export-excel">
                <i class="bi bi-file-earmark-excel"></i>
                <span>Download Rekap Excel</span>
            </a>
            <form action="{{ route('dashboard') }}" method="GET" id="dateFilterForm" class="d-inline">
                <div class="position-relative">
                    <input type="text" name="date" class="btn-date" id="flatpickr-date" value="{{ \Carbon\Carbon::parse($selectedDate)->format('Y-m-d') }}" style="cursor: pointer; min-width: 150px; text-align: center;">
                </div>
            </form>
        </div>
    </div>

    <!-- Notifikasi Transaksi Gagal -->
    @if($recentFailedOrders->isNotEmpty())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #FEE2E2; border-left: 4px solid #EF4444 !important; color: #991B1B; padding-right: 3rem;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3" style="color: #EF4444;"></i>
            <div>
                <h5 class="alert-heading mb-1 fw-bold" style="color: #991B1B;">Pemberitahuan Transaksi Gagal / Dibatalkan</h5>
                <p class="mb-0">Terdapat <strong>{{ $recentFailedOrders->count() }}</strong> transaksi terbaru yang gagal atau dibatalkan. Segera periksa detail pesanan.</p>
            </div>
        </div>
        <hr class="my-2" style="border-top-color: #FCA5A5; opacity: 0.3;">
        <ul class="mb-0 ps-3">
            @foreach($recentFailedOrders as $failedOrder)
            <li class="mb-1">
                Invoice: <strong><a href="{{ route('orders.show', $failedOrder->id) }}" style="color: #B91C1C; text-decoration: underline;">{{ $failedOrder->invoice }}</a></strong> | 
                Nama: <strong>{{ $failedOrder->customer_name ?? '-' }}</strong> | 
                Meja: <strong>{{ optional($failedOrder->table)->table_number ?? 'Kasir (Takeaway)' }}</strong> | 
                Total: <strong>Rp {{ number_format($failedOrder->total, 0, ',', '.') }}</strong> | 
                Status: <span class="badge bg-danger">{{ ucfirst($failedOrder->payment_status ?: $failedOrder->status) }}</span>
            </li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="position: absolute; right: 1rem; top: 1rem; border: none; background: none; font-size: 1.2rem;"></button>
    </div>
    @endif

    <!-- Statistik -->
    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-sm-6">
            <div class="dashboard-card stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <small>Total Kategori</small>
                    <h2>{{ $totalKategori }}</h2>
                    <a href="{{ route('categories.index') }}">Lihat Detail →</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="dashboard-card stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-cup-hot"></i>
                </div>
                <div>
                    <small>Total Menu</small>
                    <h2>{{ $totalMenu }}</h2>
                    <a href="{{ route('menus.index') }}">Lihat Detail →</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="dashboard-card stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-grid"></i>
                </div>
                <div>
                    <small>Total Meja</small>
                    <h2>{{ $totalMeja }}</h2>
                    <a href="{{ route('tables.index') }}">Lihat Detail →</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="dashboard-card stat-card">
                <div class="stat-icon purple">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <small>Total Pesanan</small>
                    <h2>{{ $totalPesanan }}</h2>
                    <a href="{{ route('orders.index') }}">Lihat Detail →</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Pendapatan -->
    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="dashboard-card stat-card revenue-card">
                <div class="stat-icon" style="background:#FFF8E1; color:#F59E0B; flex-shrink:0;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <small>Total Pendapatan (Paid)</small>
                    <h2 class="revenue-amount">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h2>
                    <a href="{{ route('orders.index') }}">Lihat Semua Pesanan →</a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card stat-card revenue-card">
                <div class="stat-icon" style="background:#E8F5E9; color:#22C55E; flex-shrink:0;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <small>Pesanan Hari Ini</small>
                    <h2>{{ $pesananHariIni }}</h2>
                    <small class="text-muted" style="color:#64748B;">
                        Pendapatan: <strong>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</strong>
                    </small>
                </div>
            </div>
        </div>

    </div>

    <!-- Grafik & Status -->
    <div class="row g-4">

        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <h4>Grafik Penjualan</h4>
                    <span class="btn-filter">7 Hari Terakhir</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <h4>Status Pesanan</h4>
                </div>

                @if($totalPesanan > 0)
                <div class="status-chart">
                    <canvas id="statusChart"></canvas>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-pie-chart fs-1 d-block mb-2"></i>
                    Belum ada data pesanan
                </div>
                @endif

                <div class="status-list">
                    <div class="status-item">
                        <div class="status-left">
                            <div class="status-dot success"></div>
                            <span>Lunas (Paid)</span>
                        </div>
                        <strong>{{ $paidOrders }}</strong>
                    </div>
                    <div class="status-item">
                        <div class="status-left">
                            <div class="status-dot warning"></div>
                            <span>Pending</span>
                        </div>
                        <strong>{{ $pendingOrders }}</strong>
                    </div>
                    <div class="status-item">
                        <div class="status-left">
                            <div class="status-dot primary"></div>
                            <span>Diproses</span>
                        </div>
                        <strong>{{ $processOrders }}</strong>
                    </div>
                    <div class="status-item">
                        <div class="status-left">
                            <div class="status-dot danger"></div>
                            <span>Gagal/Cancel</span>
                        </div>
                        <strong>{{ $failedOrders }}</strong>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Pesanan & Menu -->
    <div class="row g-4 mt-2">

        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <h4>Pesanan Terbaru</h4>
                    <a href="{{ route('orders.index') }}">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Meja</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesananTerbaru as $order)
                            <tr>
                                <td><strong>{{ $order->invoice }}</strong></td>
                                <td>{{ optional($order->table)->table_number ?? '-' }}</td>
                                <td>{{ $order->customer_name ?? '-' }}</td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->payment_status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada pesanan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <h4>Menu Terlaris</h4>
                </div>
                @forelse($menuTerlaris as $item)
                <div class="best-menu">
                    <img src="{{ optional($item->menu)->image
                        ? asset('storage/'.optional($item->menu)->image)
                        : asset('images/no-image.png') }}"
                        alt="{{ optional($item->menu)->name }}">
                    <div class="best-content">
                        <h6>{{ optional($item->menu)->name ?? 'Menu dihapus' }}</h6>
                        <small>Terjual {{ $item->total_terjual }}x</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cup-hot fs-1 d-block mb-2"></i>
                    Belum ada data.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#flatpickr-date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        altInputClass: "btn-date m-0",
        onChange: function(selectedDates, dateStr, instance) {
            document.getElementById('dateFilterForm').submit();
        }
    });
</script>
@endpush

@endsection

@push('scripts')
<script>
// =====================
// Sales Chart (Line)
// =====================
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    const labels = @json($chartLabels);
    const data   = @json($chartData);

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data,
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                borderWidth: 3,
                pointBackgroundColor: '#2563EB',
                pointRadius: 5,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#F1F5F9' },
                    ticks: { font: { size: 13 }, color: '#64748B' }
                },
                y: {
                    grid: { color: '#F1F5F9' },
                    ticks: {
                        font: { size: 13 },
                        color: '#64748B',
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                            return 'Rp ' + value;
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

// =====================
// Status Chart (Doughnut)
// =====================
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    const paid    = {{ $paidOrders }};
    const pending = {{ $pendingOrders }};
    const process = {{ $processOrders }};
    const failed  = {{ $failedOrders }};
    const total   = paid + pending + process + failed;

    if (total > 0) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Pending', 'Diproses', 'Gagal'],
                datasets: [{
                    data: [paid, pending, process, failed],
                    backgroundColor: ['#10B981', '#F59E0B', '#2563EB', '#EF4444'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + context.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
}
</script>
@endpush