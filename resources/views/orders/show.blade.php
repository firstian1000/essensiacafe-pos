@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}?v=2">
@endpush

@section('title', 'Detail Pesanan')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="order-page order-detail-page">
    <div class="page-header detail-page-header">
        <div>
            <h1>Detail Pesanan</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <a href="{{ route('orders.index') }}">Pesanan</a>
                <span>></span>
                <span>Detail</span>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="btn-back-order">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="detail-card order-detail-card">
        <div class="order-detail-top">
            <div>
                <span class="detail-eyebrow">Invoice</span>
                <h2>#{{ $order->invoice }}</h2>
                <p>{{ $order->created_at->format('d M Y H:i') }} WIB</p>
            </div>
            <div class="detail-total-box">
                <span>Total Bayar</span>
                <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-info-item">
                <span>Customer</span>
                <strong>{{ $order->customer_name ?: '-' }}</strong>
            </div>
            <div class="detail-info-item">
                <span>No WhatsApp</span>
                <strong>{{ $order->phone ?: '-' }}</strong>
            </div>
            <div class="detail-info-item">
                <span>Meja</span>
                <strong>{{ optional($order->table)->display_name ?? '-' }}</strong>
            </div>
            <div class="detail-info-item">
                <span>Metode Pembayaran</span>
                @if($order->payment_method == 'cash')
                    <strong><span class="badge bg-success"><i class="bi bi-cash-coin"></i> Cash</span></strong>
                @else
                    <strong><span class="badge bg-info text-dark"><i class="bi bi-qr-code"></i> Midtrans</span></strong>
                @endif
            </div>
            <div class="detail-info-item">
                <span>Status Pembayaran</span>
                @if($order->payment_status == 'paid')
                    <strong><span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Lunas</span></strong>
                @elseif($order->payment_status == 'pending')
                    <strong><span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span></strong>
                @else
                    <strong><span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Gagal</span></strong>
                @endif
            </div>
            <div class="detail-info-item">
                <span>Status Pesanan</span>
                @if($order->status == 'pending')
                    <strong><span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span></strong>
                @elseif($order->status == 'processing')
                    <strong><span class="badge bg-primary"><i class="bi bi-arrow-repeat"></i> Diproses</span></strong>
                @else
                    <strong><span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Selesai</span></strong>
                @endif
            </div>
        </div>

        @if($order->payment_method == 'cash' && $order->payment_status == 'pending')
            <div class="detail-action-strip">
                <span>Konfirmasi pembayaran cash jika uang sudah diterima.</span>
                <a href="{{ route('orders.paid', $order) }}" class="btn-detail-action primary">
                    <i class="bi bi-cash-coin"></i>
                    Konfirmasi Lunas
                </a>
            </div>
        @endif

        <div class="detail-section-head">
            <div>
                <i class="bi bi-list-check"></i>
                <h3>Detail Item Pesanan</h3>
            </div>
            <span>{{ $order->items->sum('qty') }} item</span>
        </div>

        <div class="table-wrapper order-detail-table-wrap">
            <table class="order-detail-items">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td data-label="Menu"><strong>{{ $item->menu->name }}</strong></td>
                        <td data-label="Qty">{{ $item->qty }}x</td>
                        <td data-label="Harga Satuan">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td data-label="Subtotal" class="price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="detail-footer-actions">
            @if($order->status == 'pending')
                <a href="{{ route('orders.process', $order->id) }}" class="btn-detail-action warning">
                    <i class="bi bi-fire"></i>
                    Proses Pesanan
                </a>
            @elseif($order->status == 'processing')
                <a href="{{ route('orders.complete', $order->id) }}" class="btn-detail-action success">
                    <i class="bi bi-check2-circle"></i>
                    Selesaikan Pesanan
                </a>
            @else
                <button class="btn-detail-action disabled" disabled>
                    <i class="bi bi-check-circle-fill"></i>
                    Pesanan Selesai
                </button>
            @endif
        </div>
    </div>
</div>
@endsection