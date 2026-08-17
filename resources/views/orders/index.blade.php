@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}?v=10">
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

@section('title', 'Pesanan')

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

<div class="order-page">
    <div class="page-header">
        <div>
            <h1>Daftar Pesanan</h1>
            <p style="color: #64748B; font-size: 14px; margin: 4px 0 8px 0; font-weight: 500;">Data pesanan online lewat meja.</p>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <span>Pesanan</span>
            </div>
        </div>
    </div>

    <div class="order-card">
        <div class="toolbar">
            <form action="{{ route('orders.index') }}" method="GET" class="toolbar-form admin-filter-row">
                <input type="hidden" name="area" value="{{ $activeArea }}">
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
                <a href="{{ route('orders.index', ['area' => $activeArea]) }}" class="btn-reset-modern"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </form>
        </div>

        <div class="table-wrapper">
            <div class="table-header">
                <div>Invoice</div>
                <div>Layanan</div>
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
                <div data-label="Layanan">
                    {{ ($order->service_type ?? 'dine_in') === 'take_away' ? 'Take Away' : (optional($order->table)->display_name ?? 'Dine In') }}
                </div>
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
                    @if(auth()->user()?->role === 'admin')
                        <a href="{{ route('orders.show', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @elseif($order->status == 'pending')
                        <a href="{{ route('orders.process', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-process" title="Mulai Proses Pesanan">
                            <i class="bi bi-play-fill"></i> Proses
                        </a>
                        @if(auth()->user()?->role === 'cashier' && $order->payment_method == 'cash' && $order->payment_status == 'pending')
                        <a href="{{ route('orders.paid', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-pay" title="Konfirmasi Cash Lunas" onclick="return confirm('Konfirmasi pembayaran cash sudah lunas?')">
                            <i class="bi bi-cash-coin"></i> Lunas
                        </a>
                        @endif
                        <a href="{{ route('orders.cancel', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-cancel" title="Batalkan Pesanan" data-cancel-order data-invoice="{{ $order->invoice }}">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <a href="{{ route('orders.show', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @elseif($order->status == 'processing')
                        <a href="{{ route('orders.complete', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-complete" title="Pesanan Selesai / Sampai ke Meja">
                            <i class="bi bi-check-lg"></i> Sampai
                        </a>
                        @if(auth()->user()?->role === 'cashier' && $order->payment_method == 'cash' && $order->payment_status == 'pending')
                        <a href="{{ route('orders.paid', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-pay" title="Konfirmasi Cash Lunas" onclick="return confirm('Konfirmasi pembayaran cash sudah lunas?')">
                            <i class="bi bi-cash-coin"></i> Lunas
                        </a>
                        @endif
                        <a href="{{ route('orders.unprocess', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-unprocess" title="Kembalikan ke Pending" onclick="return confirm('Kembalikan status pesanan ke Pending?')">
                            <i class="bi bi-arrow-counterclockwise"></i> Batal Proses
                        </a>
                        <a href="{{ route('orders.cancel', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-cancel" title="Batalkan Pesanan" data-cancel-order data-invoice="{{ $order->invoice }}">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <a href="{{ route('orders.show', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                    @elseif($order->status == 'completed')
                        <a href="{{ route('orders.show', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
                            <i class="bi bi-eye-fill"></i> Detail
                        </a>
                        <a href="{{ route('payments.receipt', $order) }}" class="btn-action-text btn-action-print" title="Cetak Struk">
                            <i class="bi bi-printer-fill"></i> Struk
                        </a>
                        @if(auth()->user()?->role === 'cashier' && $order->payment_method == 'cash' && $order->payment_status == 'pending')
                        <a href="{{ route('orders.paid', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-pay" title="Konfirmasi Cash Lunas" onclick="return confirm('Konfirmasi pembayaran cash sudah lunas?')">
                            <i class="bi bi-cash-coin"></i> Lunas
                        </a>
                        @endif
                    @elseif($order->status == 'cancelled')
                        <a href="{{ route('orders.show', ['order' => $order, 'area' => $activeArea]) }}" class="btn-action-text btn-action-detail" title="Detail Pesanan">
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
