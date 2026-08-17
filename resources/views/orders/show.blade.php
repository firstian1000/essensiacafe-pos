@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}?v=2">
<style>
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

@section('title', 'Detail Pesanan')

@section('content')

@php
    $activeArea = auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
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
            <h1>Detail Pesanan</h1>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <a href="{{ route('orders.index', ['area' => $activeArea]) }}">Pesanan</a>
                <span>></span>
                <span>Detail</span>
            </div>
        </div>
        <a href="{{ route('orders.index', ['area' => $activeArea]) }}" class="btn-back-order">
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
                <span>Layanan</span>
                <strong>
                    <span class="badge bg-info text-dark">
                        <i class="bi {{ ($order->service_type ?? 'dine_in') === 'take_away' ? 'bi-bag-check' : 'bi-cup-hot' }}"></i>
                        {{ ($order->service_type ?? 'dine_in') === 'take_away' ? 'Take Away' : 'Dine In' }}
                    </span>
                </strong>
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
                @elseif($order->status == 'completed')
                    <strong><span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Selesai</span></strong>
                @elseif($order->status == 'cancelled')
                    <strong><span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Dibatalkan</span></strong>
                @endif
            </div>
            @if($order->status === 'cancelled' && $order->cancel_reason)
            <div class="detail-info-item">
                <span>Alasan Pembatalan</span>
                <strong>{{ $order->cancel_reason }}</strong>
            </div>
            @endif
        </div>

        @if(auth()->user()?->role === 'cashier' && $order->payment_method == 'cash' && $order->payment_status == 'pending' && $order->status != 'cancelled')
            <div class="detail-action-strip">
                <span>Konfirmasi pembayaran cash jika uang sudah diterima.</span>
                <a href="{{ route('orders.paid', ['order' => $order, 'area' => $activeArea]) }}" class="btn-detail-action primary">
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

        @if(auth()->user()?->role === 'cashier')
        <div class="detail-footer-actions" style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: flex-end;">
            @if($order->status == 'pending')
                <a href="{{ route('orders.process', ['order' => $order->id, 'area' => $activeArea]) }}" class="btn-detail-action primary">
                    <i class="bi bi-play-fill me-1"></i> Proses Pesanan
                </a>
                <a href="{{ route('orders.cancel', ['order' => $order->id, 'area' => $activeArea]) }}" class="btn-detail-action danger" style="background: #FFE4E6; color: #E11D48;" data-cancel-order data-invoice="{{ $order->invoice }}">
                    <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                </a>
            @elseif($order->status == 'processing')
                <a href="{{ route('orders.complete', ['order' => $order->id, 'area' => $activeArea]) }}" class="btn-detail-action success">
                    <i class="bi bi-check2-circle me-1"></i> Pesanan Sampai / Selesai
                </a>
                <a href="{{ route('orders.unprocess', ['order' => $order->id, 'area' => $activeArea]) }}" class="btn-detail-action warning" style="background: #FFF7ED; color: #C2410C;" onclick="return confirm('Kembalikan status pesanan ke Pending?')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Batal Proses
                </a>
                <a href="{{ route('orders.cancel', ['order' => $order->id, 'area' => $activeArea]) }}" class="btn-detail-action danger" style="background: #FFE4E6; color: #E11D48;" data-cancel-order data-invoice="{{ $order->invoice }}">
                    <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                </a>
            @elseif($order->status == 'completed')
                <button class="btn-detail-action disabled" disabled>
                    <i class="bi bi-check-circle-fill me-1"></i> Pesanan Selesai
                </button>
            @elseif($order->status == 'cancelled')
                <button class="btn-detail-action disabled" style="background: #FFE4E6; color: #E11D48; border: 0;" disabled>
                    <i class="bi bi-x-circle-fill me-1"></i> Pesanan Dibatalkan
                </button>
            @endif
        </div>
        @endif
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
