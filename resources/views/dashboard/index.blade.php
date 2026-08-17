@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}?v=13">
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
    </div>

    <!-- Statistik -->
    <div class="dashboard-stats-grid mb-4">

        <div>
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

        <div>
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

        <div>
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

        <div>
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
    <div class="dashboard-money-grid mb-4">
        <div class="dashboard-card stat-card money-stat-card">
            <div class="stat-icon purple">
                <i class="bi bi-credit-card-2-front"></i>
            </div>
            <div>
                <small>Total Pengeluaran</small>
                <h2 class="money-stat-value money-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
                <a href="{{ route('expenses.index') }}">Kelola Pengeluaran</a>
            </div>
        </div>

        <div class="dashboard-card stat-card revenue-card money-stat-card">
            <div class="stat-icon" style="background:#FFF8E1; color:#F59E0B; flex-shrink:0;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <small>Total Pendapatan</small>
                <h2 class="money-stat-value money-green">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h2>
                <a href="{{ route('orders.index') }}">Lihat Pesanan</a>
            </div>
        </div>

        <div class="dashboard-card stat-card money-stat-card">
            <div class="stat-icon {{ $keuntungan >= 0 ? 'blue' : 'red' }}">
                <i class="bi {{ $keuntungan >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
            </div>
            <div>
                <small>Keuntungan</small>
                <h2 class="money-stat-value {{ $keuntungan >= 0 ? 'profit-plus' : 'profit-minus' }}">
                    {{ $keuntungan >= 0 ? '+' : '-' }} Rp {{ number_format(abs($keuntungan), 0, ',', '.') }}
                </h2>
                <span class="mini-caption">Pendapatan dikurangi pengeluaran</span>
            </div>
        </div>
    </div>
    <div class="dashboard-card mb-4">
        <form action="{{ route('dashboard') }}" method="GET" class="dashboard-filter-row">
            <label>
                <span>Kalender</span>
                <input type="text" name="date" class="btn-date" id="flatpickr-date" value="{{ \Carbon\Carbon::parse($selectedDate)->format('Y-m-d') }}">
            </label>
            <label>
                <span>Pembayaran</span>
                <select name="payment_filter" onchange="this.form.submit()">
                    <option value="all" {{ $paymentFilter === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="cash" {{ $paymentFilter === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="non_cash" {{ $paymentFilter === 'non_cash' ? 'selected' : '' }}>Non Tunai</option>
                </select>
            </label>
            <label>
                <span>Brand</span>
                <select name="brand_filter" onchange="this.form.submit()">
                    <option value="all" {{ $brandFilter === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="essensia" {{ $brandFilter === 'essensia' ? 'selected' : '' }}>Essensia</option>
                    <option value="buncha" {{ $brandFilter === 'buncha' ? 'selected' : '' }}>Buncha</option>
                </select>
            </label>
            <button type="submit" class="btn-filter-apply">
                <i class="bi bi-funnel-fill"></i>
                Terapkan
            </button>
            <a href="{{ route('dashboard', ['date' => $selectedDate]) }}" class="btn-filter-reset">
                <i class="bi bi-arrow-counterclockwise"></i>
                Reset
            </a>
            <div class="export-actions" style="display:flex; gap:8px; align-items:center;">
                <a href="{{ route('dashboard.export', [
                    'date' => $selectedDate,
                    'payment_filter' => $paymentFilter,
                    'brand_filter' => $brandFilter,
                    'format' => 'excel',
                ]) }}" class="btn-export-excel" title="Download Excel" aria-label="Download Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                    Excel
                </a>
                <a href="{{ route('dashboard.export', [
                    'date' => $selectedDate,
                    'payment_filter' => $paymentFilter,
                    'brand_filter' => $brandFilter,
                    'format' => 'pdf',
                ]) }}" class="btn-export-excel" title="Download PDF" aria-label="Download PDF" style="background:#DC2626;">
                    <i class="bi bi-file-earmark-pdf"></i>
                    PDF
                </a>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <h4>Ringkasan Stok</h4>
                    <a href="{{ route('stocks.index') }}">Lihat Semua</a>
                </div>
                <div class="stock-alert-strip">
                    <div><strong>{{ $lowStockCount }}</strong><span>Stok Rendah</span></div>
                    <div><strong>{{ $emptyStockCount }}</strong><span>Stok Habis</span></div>
                </div>
                <div class="dashboard-stock-list">
                    @forelse($stockMenus as $menu)
                    @php
                        $stockStatus = $menu->stock <= 0 ? 'Stok Habis' : ($menu->stock <= 5 ? 'Stok Rendah' : 'Aman');
                        $stockClass = $menu->stock <= 0 ? 'danger' : ($menu->stock <= 5 ? 'warning' : 'success');
                    @endphp
                    <div class="dashboard-stock-item">
                        <span class="dashboard-stock-icon"><i class="bi bi-box-seam"></i></span>
                        <div>
                            <strong>{{ $menu->name }}</strong>
                            <span>Stok internal</span>
                        </div>
                        <b>{{ $menu->stock }}</b>
                        <em class="{{ $stockClass }}">{{ $stockStatus }}</em>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">Belum ada stok yang dibatasi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Status -->
    <div class="row g-4">

        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <div>
                        <h4>Pemasukan Berdasarkan Filter</h4>
                        <small style="color:#64748B;font-weight:800;">
                            {{ $paymentFilter === 'cash' ? 'Tunai' : ($paymentFilter === 'non_cash' ? 'Non Tunai' : 'Semua Pembayaran') }}
                            -
                            {{ $brandFilter === 'buncha' ? 'Buncha (Dimsum)' : ($brandFilter === 'essensia' ? 'Essensia' : 'Semua Brand') }}
                        </small>
                    </div>
                    <span class="btn-filter">Total: Rp {{ number_format($filteredRevenueTotal, 0, ',', '.') }}</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="filteredRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <div>
                        <h4>Grafik Penjualan</h4>
                        <small style="color:#64748B;font-weight:800;">
                            {{ $paymentFilter === 'cash' ? 'Tunai' : ($paymentFilter === 'non_cash' ? 'Non Tunai' : 'Semua Pembayaran') }}
                            -
                            {{ $brandFilter === 'buncha' ? 'Buncha (Dimsum)' : ($brandFilter === 'essensia' ? 'Essensia' : 'Semua Brand') }}
                        </small>
                    </div>
                    <span class="btn-filter">7 Hari Terakhir</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card h-100">
                <div class="card-header-dashboard">
                    <h4>Penjualan per Kategori</h4>
                    <span class="btn-filter">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</span>
                </div>
                @if($categorySales->sum('total_revenue') > 0)
                <div class="category-sales-layout">
                    <div class="category-donut">
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                    <div class="category-sales-list">
                        @foreach($categorySales as $category)
                        @php
                            $categoryTotal = max(1, $categorySales->sum('total_revenue'));
                            $percent = round(((int) $category->total_revenue / $categoryTotal) * 100);
                        @endphp
                        <div class="category-sales-item">
                            <span>
                                <i style="background: {{ ['#2563EB','#16A34A','#F59E0B','#8B5CF6','#EF4444','#14B8A6'][$loop->index % 6] }}"></i>
                                {{ $category->category_name }}
                            </span>
                            <strong>{{ $percent }}%</strong>
                            <em>Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</em>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-pie-chart fs-1 d-block mb-2"></i>
                    Belum ada penjualan paid di tanggal ini
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="card-header-dashboard">
                    <div>
                        <h4>Status Pesanan</h4>
                        <small style="color:#64748B;font-weight:800;">Mengikuti filter aktif</small>
                    </div>
                </div>

                @if($filteredStatusTotal > 0)
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
            instance.input.closest('form').submit();
        }
    });
</script>
@endpush

@endsection

@push('scripts')
<script>
// =====================
// Filtered Revenue Chart (Bar)
// =====================
const filteredRevenueCtx = document.getElementById('filteredRevenueChart');
if (filteredRevenueCtx) {
    new Chart(filteredRevenueCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: @json($filteredRevenueData),
                backgroundColor: '#F59E0B',
                borderColor: '#D97706',
                borderWidth: 1,
                borderRadius: 10,
                maxBarThickness: 58,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    grid: { display: false },
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
// Category Sales Chart (Doughnut)
// =====================
const categorySalesCtx = document.getElementById('categorySalesChart');
if (categorySalesCtx) {
    const categorySales = @json($categorySales->pluck('total_revenue')->map(fn ($value) => (int) $value)->values());
    const categoryLabels = @json($categorySales->pluck('category_name')->values());
    const categoryTotal = categorySales.reduce((sum, value) => sum + Number(value || 0), 0);

    if (categoryTotal > 0) {
        new Chart(categorySalesCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categorySales,
                    backgroundColor: ['#2563EB', '#16A34A', '#F59E0B', '#8B5CF6', '#EF4444', '#14B8A6'],
                    borderColor: '#fff',
                    borderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const pct = categoryTotal > 0 ? ((context.parsed / categoryTotal) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
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
