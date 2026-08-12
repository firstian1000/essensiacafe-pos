@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}?v=12">
@endpush

@section('title', 'Detail Pembayaran')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="payment-page payment-detail-page">
    <div class="page-header payment-detail-header">
        <div>
            <h1>Detail Pembayaran</h1>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <a href="{{ route('payments.index') }}">Pembayaran</a>
                <span>></span>
                <span>Detail</span>
            </div>
        </div>
        <a href="{{ route('payments.index') }}" class="btn-back-payment">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="detail-card payment-detail-card">
        <div class="payment-detail-top">
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

        <div class="payment-detail-grid">
            <section class="payment-detail-section">
                <div class="section-title-clean">
                    <i class="bi bi-receipt-cutoff"></i>
                    <h3>Informasi Pesanan</h3>
                </div>
                <div class="detail-info-list">
                    <div><span>Customer</span><strong>{{ $order->customer_name ?: '-' }}</strong></div>
                    <div><span>No WhatsApp</span><strong>{{ $order->phone ?: '-' }}</strong></div>
                    <div><span>Meja</span><strong>{{ optional($order->table)->display_name ?? '-' }}</strong></div>
                    <div>
                        <span>Status Pesanan</span>
                        @if($order->status == 'pending')
                            <strong><span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Pending</span></strong>
                        @elseif($order->status == 'processing')
                            <strong><span class="status-badge status-process"><i class="bi bi-arrow-repeat"></i> Diproses</span></strong>
                        @else
                            <strong><span class="status-badge status-paid"><i class="bi bi-check-circle-fill"></i> Selesai</span></strong>
                        @endif
                    </div>
                </div>
            </section>

            <section class="payment-detail-section">
                <div class="section-title-clean">
                    <i class="bi bi-credit-card"></i>
                    <h3>Informasi Pembayaran</h3>
                </div>
                <div class="detail-info-list">
                    <div>
                        <span>Metode</span>
                        @if($order->payment_method == 'cash')
                            <strong><span class="badge-method badge-cash"><i class="bi bi-cash-coin"></i> Cash</span></strong>
                        @else
                            <strong><span class="badge-method badge-qris"><i class="bi bi-qr-code"></i> Midtrans</span></strong>
                        @endif
                    </div>
                    <div>
                        <span>Status Bayar</span>
                        @if($order->payment_status == 'paid')
                            <strong><span class="status-badge status-paid"><i class="bi bi-check-circle-fill"></i> Lunas</span></strong>
                        @elseif($order->payment_status == 'pending')
                            <strong><span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Pending</span></strong>
                        @else
                            <strong><span class="status-badge status-failed"><i class="bi bi-x-circle-fill"></i> Gagal</span></strong>
                        @endif
                    </div>
                    <div><span>Total Bayar</span><strong class="price-big">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
                </div>

                @if(auth()->user()?->role === 'cashier' && $order->payment_status == 'pending')
                    <a href="{{ route('orders.paid', $order) }}"
                       class="btn-confirm-pay clean"
                       onclick="return confirm('Konfirmasi pembayaran lunas?')">
                        <i class="bi bi-check-circle-fill"></i>
                        Konfirmasi Lunas
                    </a>
                @endif

                @if($order->payment_status == 'paid')
                    <div class="paid-stamp clean">
                        <i class="bi bi-patch-check-fill"></i>
                        LUNAS
                    </div>
                    @if(auth()->user()?->role === 'cashier')
                    <a href="{{ route('payments.receipt', $order) }}"
                       class="btn-confirm-pay clean" style="background:#2E7DB8; margin-top:10px;">
                        <i class="bi bi-printer-fill"></i>
                        Cetak Nota
                    </a>
                    @endif
                @endif
            </section>
        </div>

        <div class="detail-items-section clean">
            <div class="section-title-clean items-head">
                <i class="bi bi-list-check"></i>
                <h3>Item Pesanan</h3>
                <span>{{ $order->items->sum('qty') }} item</span>
            </div>
            <div class="table-responsive payment-detail-table-wrap">
                <table class="payment-detail-items">
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
                            <td><strong>{{ optional($item->menu)->name ?? 'Menu dihapus' }}</strong></td>
                            <td>{{ $item->qty }}x</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="price-cell">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td class="price-big">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
