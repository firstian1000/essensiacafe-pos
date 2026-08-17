@extends('layouts.admin')

@section('title', 'Pembayaran QRIS')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}?v=12">
<style>
    .qris-page {
        display: grid;
        gap: 20px;
        grid-template-columns: minmax(280px, 0.9fr) minmax(360px, 1.1fr);
        align-items: start;
    }

    .qris-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 22px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    .qris-total {
        margin: 18px 0;
        padding: 18px;
        background: #F8FAFC;
        border-radius: 8px;
    }

    .qris-total span,
    .qris-meta span {
        display: block;
        color: #64748B;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .qris-total strong {
        color: #0F172A;
        font-size: 30px;
        line-height: 1.1;
    }

    .qris-meta {
        display: grid;
        gap: 12px;
    }

    .qris-status {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding: 14px;
        border-radius: 8px;
        background: #FEF3C7;
        color: #92400E;
        font-weight: 800;
    }

    .qris-status.is-paid {
        background: #DCFCE7;
        color: #166534;
    }

    .qris-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .qris-actions a,
    .qris-actions button {
        border: 0;
        border-radius: 8px;
        padding: 11px 14px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .qris-primary {
        background: #2563EB;
        color: #fff;
    }

    .qris-secondary {
        background: #E2E8F0;
        color: #0F172A;
    }

    #snap-container {
        min-height: 560px;
        border: 1px dashed #CBD5E1;
        border-radius: 8px;
        overflow: hidden;
        background: #F8FAFC;
    }

    @media (max-width: 900px) {
        .qris-page {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="payment-page">
    <div class="page-header payment-detail-header">
        <div>
            <h1>Pembayaran QRIS</h1>
            <div class="breadcrumb-custom">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <a href="{{ route('cashier.index') }}">Kasir</a>
                <span>></span>
                <span>QRIS</span>
            </div>
        </div>
        <a href="{{ route('cashier.index') }}" class="btn-back-payment">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="qris-page">
        <section class="qris-card">
            <span class="detail-eyebrow">Invoice</span>
            <h2>#{{ $order->invoice }}</h2>

            <div class="qris-total">
                <span>Total Pembayaran</span>
                <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>

            <div class="qris-meta">
                <div><span>Customer</span><strong>{{ $order->customer_name ?: '-' }}</strong></div>
                <div><span>Layanan</span><strong>{{ ($order->service_type ?? 'take_away') === 'dine_in' ? 'Dine In' : 'Take Away' }}</strong></div>
                <div><span>Jumlah Item</span><strong>{{ $order->items->sum('qty') }} item</strong></div>
            </div>

            <div class="qris-status {{ $order->payment_status === 'paid' ? 'is-paid' : '' }}" id="qrisStatus">
                <i class="bi {{ $order->payment_status === 'paid' ? 'bi-check-circle-fill' : 'bi-qr-code-scan' }}"></i>
                <span>{{ $order->payment_status === 'paid' ? 'Pembayaran berhasil' : 'Menunggu pelanggan scan dan bayar' }}</span>
            </div>

            <div class="qris-actions">
                <a class="qris-secondary" href="{{ route('payments.show', $order) }}">Detail</a>
                <a class="qris-primary" id="receiptLink" href="{{ route('payments.receipt', $order) }}" style="{{ $order->payment_status === 'paid' ? '' : 'display:none;' }}">Cetak Nota</a>
                <button type="button" class="qris-primary" id="reloadSnap">Muat Ulang QRIS</button>
            </div>
        </section>

        <section class="qris-card">
            <div id="snap-container"></div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ config('midtrans.isProduction') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
const snapToken = @json($order->snap_token);
const statusUrl = @json(route('order.success.status', $order));
const receiptUrl = @json(route('payments.receipt', $order));
const qrisStatus = document.getElementById('qrisStatus');
const receiptLink = document.getElementById('receiptLink');
const reloadSnap = document.getElementById('reloadSnap');

function setPaid() {
    qrisStatus.classList.add('is-paid');
    qrisStatus.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Pembayaran berhasil, nota siap dicetak</span>';
    receiptLink.style.display = '';
    setTimeout(() => {
        window.location.href = receiptUrl;
    }, 1200);
}

function renderSnap() {
    const container = document.getElementById('snap-container');
    container.innerHTML = '';

    if (typeof snap === 'undefined') {
        container.innerHTML = '<div style="padding:20px;color:#B91C1C;font-weight:800;">Snap Midtrans gagal dimuat.</div>';
        return;
    }

    snap.embed(snapToken, {
        embedId: 'snap-container',
        onSuccess: setPaid,
        onPending: function () {
            qrisStatus.innerHTML = '<i class="bi bi-clock-fill"></i><span>Pembayaran masih diproses</span>';
        },
        onError: function () {
            qrisStatus.innerHTML = '<i class="bi bi-x-circle-fill"></i><span>Pembayaran gagal, coba muat ulang QRIS</span>';
        },
        onClose: function () {
            qrisStatus.innerHTML = '<i class="bi bi-qr-code-scan"></i><span>Menunggu pelanggan scan dan bayar</span>';
        }
    });
}

async function pollPaymentStatus() {
    try {
        const response = await fetch(statusUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            cache: 'no-store',
        });

        if (!response.ok) return;

        const data = await response.json();

        if (data.payment_status === 'paid') {
            setPaid();
        } else if (data.payment_status === 'failed' || data.payment_status === 'expired') {
            qrisStatus.classList.remove('is-paid');
            qrisStatus.innerHTML = '<i class="bi bi-x-circle-fill"></i><span>Pembayaran gagal atau kedaluwarsa</span>';
        }
    } catch (error) {
        console.log('Gagal mengecek status QRIS:', error);
    }
}

reloadSnap.addEventListener('click', renderSnap);
window.addEventListener('load', renderSnap);
setInterval(pollPaymentStatus, 3000);
</script>
@endpush
