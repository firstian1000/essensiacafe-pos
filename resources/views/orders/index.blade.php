@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}?v=10">
@endpush

@section('title', 'Pesanan')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="order-page">
    <div class="page-header">
        <div>
            <h1>Daftar Pesanan</h1>
            <p style="color: #64748B; font-size: 14px; margin: 4px 0 8px 0; font-weight: 500;">Data pesanan online lewat meja.</p>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <span>Pesanan</span>
            </div>
        </div>
    </div>

    <div class="order-card">
        <div class="toolbar">
            <form action="{{ route('orders.index') }}" method="GET" class="toolbar-form admin-filter-row">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / meja / customer...">
                </div>
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status Pesanan</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
                <select name="payment_status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status Bayar</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
                <button type="submit" class="btn-search"><i class="bi bi-arrow-clockwise"></i> Filter</button>
                <a href="{{ route('orders.index') }}" class="btn-reset-modern"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </form>
        </div>

        <div class="table-wrapper">
            <div class="table-header">
                <div>Invoice</div>
                <div>Meja</div>
                <div>Customer</div>
                <div>Total</div>
                <div>Metode Bayar</div>
                <div>Status Bayar</div>
                <div>Status Pesanan</div>
                <div>Waktu</div>
                <div>Aksi</div>
            </div>

            @forelse($orders as $order)
            <div class="order-item">
                <div class="invoice-num" data-label="Invoice">#{{ $order->invoice }}</div>
                <div data-label="Meja">{{ optional($order->table)->display_name ?? '-' }}</div>
                <div class="customer-name" data-label="Customer">{{ $order->customer_name }}</div>
                <div class="price" data-label="Total">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                <div data-label="Metode Bayar">
                    @if($order->payment_method == 'cash')
                        <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Cash</span>
                    @else
                        <span class="badge bg-info text-dark"><i class="bi bi-qr-code"></i> Midtrans</span>
                    @endif
                </div>
                <div data-label="Status Bayar">
                    @if($order->payment_status == 'paid')
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                    @elseif($order->payment_status == 'pending')
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Gagal</span>
                    @endif
                </div>
                <div data-label="Status Pesanan">
                    @if($order->status == 'pending')
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span>
                    @elseif($order->status == 'processing')
                        <span class="badge bg-primary"><i class="bi bi-arrow-repeat"></i> Diproses</span>
                    @elseif($order->status == 'completed')
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                    @elseif($order->status == 'cancelled')
                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Dibatalkan</span>
                    @endif
                </div>
                <div class="time" data-label="Waktu">{{ $order->created_at->format('d M H:i') }}</div>
                <div data-label="Aksi" class="action-buttons">
                    @if($order->status == 'pending')
                        <a href="{{ route('orders.process', $order) }}" class="btn-action-text btn-action-process" title="Mulai Proses Pesanan">
                            <i class="bi bi-play-fill"></i> Proses
                        </a>
                        <a href="{{ route('orders.cancel', $order) }}" class="btn-action-text btn-action-cancel" title="Batalkan Pesanan" onclick="return confirm('Yakin ingin membatalkan pesanan #{{ $order->invoice }}?')">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <a href="{{ route('orders.show', $order) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @elseif($order->status == 'processing')
                        <a href="{{ route('orders.complete', $order) }}" class="btn-action-text btn-action-complete" title="Pesanan Selesai / Sampai ke Meja">
                            <i class="bi bi-check-lg"></i> Sampai
                        </a>
                        <a href="{{ route('orders.unprocess', $order) }}" class="btn-action-text btn-action-unprocess" title="Kembalikan ke Pending" onclick="return confirm('Kembalikan status pesanan ke Pending?')">
                            <i class="bi bi-arrow-counterclockwise"></i> Batal Proses
                        </a>
                        <a href="{{ route('orders.cancel', $order) }}" class="btn-action-text btn-action-cancel" title="Batalkan Pesanan" onclick="return confirm('Yakin ingin membatalkan pesanan #{{ $order->invoice }}?')">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <a href="{{ route('orders.show', $order) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @elseif($order->status == 'completed')
                        <a href="{{ route('orders.show', $order) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                        <a href="{{ route('payments.receipt', $order) }}" class="btn-action-text btn-action-print" title="Cetak Struk">
                            <i class="bi bi-printer-fill"></i> Struk
                        </a>
                        @if($order->payment_status == 'pending')
                        <a href="{{ route('orders.paid', $order) }}" class="btn-action-text btn-action-pay" title="Konfirmasi Bayar" onclick="return confirm('Konfirmasi pembayaran lunas?')">
                            <i class="bi bi-cash-coin"></i> Bayar
                        </a>
                        @endif
                    @elseif($order->status == 'cancelled')
                        <a href="{{ route('orders.show', $order) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty">
                <i class="bi bi-receipt fs-1 d-block mb-3"></i>
                Belum ada pesanan.
            </div>
            @endforelse
        </div>

        @if(method_exists($orders, 'links'))
        <div class="pagination-wrapper">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection