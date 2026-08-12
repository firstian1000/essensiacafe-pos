@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}?v=11">
@endpush

@section('title', 'Manajemen Pembayaran')

@section('content')

<div class="payment-page">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1>Manajemen Pembayaran</h1>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <span>Pembayaran</span>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="payment-filter-card">
        <form action="{{ route('payments.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label>Cari Transaksi</label>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / meja / customer...">
                </div>
            </div>
            <div class="filter-group filter-date-single">
                <label>Tanggal</label>
                <input type="date" name="date" value="{{ request('date', $dateFrom) }}" class="filter-input">
            </div>
            <div class="filter-group">
                <label>Status Bayar</label>
                <select name="payment_status" class="filter-input">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Metode Bayar</label>
                <select name="payment_method" class="filter-input">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>Midtrans (QRIS/Transfer)</option>
                </select>
            </div>
            <button type="submit" class="btn-filter-apply">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <a href="{{ route('payments.index') }}" class="btn-filter-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-sm-6">
            <div class="pay-stat-card pay-stat-green">
                <div class="pay-stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="pay-stat-body">
                    <small>Total Pendapatan (Periode)</small>
                    <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="pay-stat-card pay-stat-blue">
                <div class="pay-stat-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="pay-stat-body">
                    <small>Total Transaksi</small>
                    <h3>{{ $totalTransaksi }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="pay-stat-card pay-stat-success">
                <div class="pay-stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="pay-stat-body">
                    <small>Transaksi Lunas</small>
                    <h3>{{ $transaksiPaid }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="pay-stat-card pay-stat-warning">
                <div class="pay-stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="pay-stat-body">
                    <small>Pending / Gagal</small>
                    <h3>{{ $transaksiPending + $transaksiGagal }}</h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Rekap Metode Pembayaran -->
    @if($rekapMetode->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="payment-card">
                <div class="payment-card-header">
                    <h4><i class="bi bi-pie-chart-fill me-2"></i>Rekap per Metode Pembayaran (Lunas)</h4>
                </div>
                <div class="rekap-metode">
                    @foreach($rekapMetode as $rekap)
                    <div class="rekap-item {{ $rekap->payment_method == 'cash' ? 'rekap-cash' : 'rekap-qris' }}">
                        <div class="rekap-icon">
                            <i class="bi {{ $rekap->payment_method == 'cash' ? 'bi-cash-coin' : 'bi-qr-code-scan' }}"></i>
                        </div>
                        <div class="rekap-info">
                            <strong>{{ $rekap->payment_method == 'cash' ? 'Cash' : 'Midtrans (QRIS/Transfer)' }}</strong>
                            <p>{{ $rekap->jumlah }} transaksi</p>
                        </div>
                        <div class="rekap-amount">
                            Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Transaksi -->
    <div class="payment-card">
        <div class="payment-card-header">
            <h4><i class="bi bi-table me-2"></i>Daftar Transaksi</h4>
            <span class="badge bg-secondary">{{ $orders->total() }} transaksi</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle payment-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Meja</th>
                        <th>Metode Bayar</th>
                        <th>Total</th>
                        <th>Status Bayar</th>
                        <th>Status Pesanan</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong class="invoice-text">{{ $order->invoice }}</strong></td>
                        <td>{{ $order->customer_name ?? '-' }}</td>
                        <td>{{ optional($order->table)->display_name ?? '-' }}</td>
                        <td>
                            @if($order->payment_method == 'cash')
                                <span class="badge-method badge-cash"><i class="bi bi-cash-coin"></i> Cash</span>
                            @else
                                <span class="badge-method badge-qris"><i class="bi bi-qr-code"></i> Midtrans</span>
                            @endif
                        </td>
                        <td class="price-cell">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @if($order->payment_status == 'paid')
                                <span class="status-badge status-paid"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                            @elseif($order->payment_status == 'pending')
                                <span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Pending</span>
                            @else
                                <span class="status-badge status-failed"><i class="bi bi-x-circle-fill"></i> Gagal</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Pending</span>
                            @elseif($order->status == 'processing')
                                <span class="status-badge status-process"><i class="bi bi-arrow-repeat"></i> Diproses</span>
                            @else
                                <span class="status-badge status-paid"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                            @endif
                        </td>
                        <td class="time-cell">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('payments.show', $order) }}" class="btn-action-text btn-action-detail" title="Detail Transaksi">
                                    <i class="bi bi-eye-fill"></i> Detail
                                </a>
                                @if(auth()->user()?->role === 'cashier' && $order->payment_status == 'pending')
                                <a href="{{ route('orders.paid', $order) }}" class="btn-action-text btn-action-pay" title="Konfirmasi Pembayaran Lunas"
                                   onclick="return confirm('Konfirmasi pembayaran lunas untuk pesanan ini?')">
                                    <i class="bi bi-check-circle-fill"></i> Lunasi
                                </a>
                                @elseif(auth()->user()?->role === 'cashier' && $order->payment_status == 'paid')
                                <a href="{{ route('payments.receipt', $order) }}" class="btn-action-text btn-action-print" title="Cetak Struk">
                                    <i class="bi bi-printer-fill"></i> Struk
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <span class="text-muted">Tidak ada transaksi pada periode ini.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'links'))
        <div class="pagination-wrapper">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif

    </div>

</div>

@endsection
