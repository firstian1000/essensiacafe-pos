@extends('layouts.admin')

@section('title', 'Rekap Pembayaran')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}?v=10">
<style>
    .recap-status-hero {
        margin-bottom: 18px;
        padding: 24px;
        border-radius: 8px;
        background: #DCFCE7;
        color: #166534;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .recap-status-hero i {
        font-size: 42px;
    }

    .recap-status-hero h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        color: #14532D;
    }

    .recap-status-hero p {
        margin: 4px 0 0;
        font-weight: 700;
    }

    .recap-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 22px;
    }

    .cancel-reason-modal {
        position: fixed;
        inset: 0;
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.48);
    }

    .cancel-reason-modal.is-open {
        display: flex;
    }

    .cancel-reason-dialog {
        width: min(420px, 100%);
        background: #fff;
        border-radius: 8px;
        padding: 22px;
        box-shadow: 0 20px 48px rgba(15, 23, 42, 0.22);
    }

    .cancel-reason-dialog h3 {
        margin: 0 0 6px;
        color: #0F172A;
        font-size: 20px;
        font-weight: 900;
    }

    .cancel-reason-dialog p {
        margin: 0 0 16px;
        color: #64748B;
        font-size: 14px;
    }

    .cancel-reason-options {
        display: grid;
        gap: 10px;
    }

    .cancel-reason-options button,
    .cancel-reason-close {
        border: 0;
        border-radius: 8px;
        padding: 12px 14px;
        font-weight: 800;
        cursor: pointer;
    }

    .cancel-reason-options button {
        background: #FFE4E6;
        color: #BE123C;
        text-align: left;
    }

    .cancel-reason-close {
        width: 100%;
        margin-top: 12px;
        background: #E2E8F0;
        color: #0F172A;
    }
</style>
@endpush

@section('content')
@php
    $activeArea = auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
    $isCash = $order->payment_method === 'cash';
    $paidAmount = (float) request('paid', $order->total);
    $changeAmount = max($paidAmount - (float) $order->total, 0);
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="order-page order-detail-page">
    <div class="page-header detail-page-header">
        <div>
            <h1>Rekap Pembayaran</h1>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <a href="{{ route('cashier.index') }}">Kasir</a>
                <span>></span>
                <span>Rekap</span>
            </div>
        </div>
    </div>

    <div class="recap-status-hero">
        <i class="bi {{ $order->payment_status === 'paid' ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
        <div>
            <h2>{{ $order->payment_status === 'paid' ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran' }}</h2>
            <p>Invoice #{{ $order->invoice }} - {{ $order->created_at->format('d M Y H:i') }} WIB</p>
        </div>
    </div>

    <div class="detail-card order-detail-card">
        <div class="order-detail-top">
            <div>
                <span class="detail-eyebrow">Customer</span>
                <h2>{{ $order->customer_name ?: '-' }}</h2>
                <p>{{ ($order->service_type ?? 'take_away') === 'dine_in' ? 'Dine In' : 'Take Away' }}</p>
            </div>
            <div class="detail-total-box">
                <span>Total Bayar</span>
                <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-info-item">
                <span>Metode Pembayaran</span>
                <strong>
                    @if($isCash)
                        <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Tunai</span>
                    @else
                        <span class="badge bg-info text-dark"><i class="bi bi-qr-code"></i> Midtrans</span>
                    @endif
                </strong>
            </div>
            <div class="detail-info-item">
                <span>Status Pembayaran</span>
                <strong>
                    @if($order->payment_status === 'paid')
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                    @else
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span>
                    @endif
                </strong>
            </div>
            <div class="detail-info-item">
                <span>Status Pesanan</span>
                <strong>{{ ucfirst($order->status) }}</strong>
            </div>
            <div class="detail-info-item">
                <span>Jumlah Item</span>
                <strong>{{ $order->items->sum('qty') }} item</strong>
            </div>
            @if($isCash)
            <div class="detail-info-item">
                <span>Uang Diterima</span>
                <strong>Rp {{ number_format($paidAmount, 0, ',', '.') }}</strong>
            </div>
            <div class="detail-info-item">
                <span>Kembalian</span>
                <strong>Rp {{ number_format($changeAmount, 0, ',', '.') }}</strong>
            </div>
            @endif
        </div>

        <div class="detail-section-head">
            <div>
                <i class="bi bi-list-check"></i>
                <h3>Item Pesanan</h3>
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
                        <td data-label="Menu">
                            <strong>{{ $item->menu?->name ?? 'Menu dihapus' }}</strong>
                            @if($item->variant_name)
                                <br><small>Varian: {{ $item->variant_name }}</small>
                            @endif
                        </td>
                        <td data-label="Qty">{{ $item->qty }}x</td>
                        <td data-label="Harga Satuan">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td data-label="Subtotal" class="price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="recap-actions">
            <a href="{{ route('payments.receipt', ['order' => $order, 'paid' => $paidAmount]) }}" class="btn-detail-action primary">
                <i class="bi bi-printer-fill"></i> Cetak Nota
            </a>
            @if(auth()->user()?->role === 'cashier' && $order->status !== 'cancelled')
                <a href="{{ route('orders.cancel', ['order' => $order, 'area' => $activeArea]) }}" class="btn-detail-action danger" style="background: #FFE4E6; color: #E11D48;" data-cancel-order data-invoice="{{ $order->invoice }}">
                    <i class="bi bi-x-circle"></i> Batalkan
                </a>
            @endif
        </div>
    </div>
</div>

<div class="cancel-reason-modal" id="cancelReasonModal" aria-hidden="true">
    <div class="cancel-reason-dialog" role="dialog" aria-modal="true" aria-labelledby="cancelReasonTitle">
        <h3 id="cancelReasonTitle">Alasan Pembatalan</h3>
        <p id="cancelReasonText">Pilih alasan untuk membatalkan pesanan.</p>
        <div class="cancel-reason-options">
            <button type="button" data-cancel-reason="Ganti Pesanan">Ganti Pesanan</button>
            <button type="button" data-cancel-reason="Ganti Pembayaran">Ganti Pembayaran</button>
            <button type="button" data-cancel-reason="Lain lain">Lain lain</button>
        </div>
        <button type="button" class="cancel-reason-close" id="cancelReasonClose">Tutup</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('cancelReasonModal');
    const closeButton = document.getElementById('cancelReasonClose');
    const text = document.getElementById('cancelReasonText');
    let cancelUrl = '';

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        cancelUrl = '';
    }

    document.querySelectorAll('[data-cancel-order]').forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            cancelUrl = button.href;
            text.textContent = `Pilih alasan untuk membatalkan pesanan #${button.dataset.invoice}.`;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });
    });

    document.querySelectorAll('[data-cancel-reason]').forEach(button => {
        button.addEventListener('click', () => {
            if (!cancelUrl) return;
            const url = new URL(cancelUrl, window.location.origin);
            url.searchParams.set('cancel_reason', button.dataset.cancelReason);
            window.location.href = url.toString();
        });
    });

    closeButton.addEventListener('click', closeModal);
    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeModal();
    });
})();
</script>
@endpush
