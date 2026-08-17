@extends('layouts.admin')

@section('title', 'Pembayaran Non Tunai')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}?v=12">
<style>
    .midtrans-wait-page {
        display: grid;
        place-items: center;
        min-height: calc(100vh - 180px);
    }

    .midtrans-wait-card {
        width: min(620px, 100%);
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
    }

    .midtrans-wait-icon {
        display: inline-grid;
        place-items: center;
        width: 72px;
        height: 72px;
        margin-bottom: 18px;
        border-radius: 50%;
        background: #DBEAFE;
        color: #2563EB;
        font-size: 34px;
    }

    .midtrans-wait-card h1 {
        margin: 0 0 8px;
        color: #0F172A;
        font-size: 26px;
        font-weight: 900;
    }

    .midtrans-wait-card p {
        margin: 0;
        color: #64748B;
        font-weight: 600;
    }

    .midtrans-total {
        margin: 20px 0;
        padding: 16px;
        border-radius: 8px;
        background: #F8FAFC;
    }

    .midtrans-total span {
        display: block;
        color: #64748B;
        font-size: 13px;
        font-weight: 800;
    }

    .midtrans-total strong {
        color: #0F172A;
        font-size: 30px;
        font-weight: 900;
    }

    .midtrans-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .midtrans-actions a,
    .midtrans-actions button {
        border: 0;
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
    }

    .midtrans-primary {
        background: #2563EB;
        color: #fff;
    }

    .midtrans-secondary {
        background: #E2E8F0;
        color: #0F172A;
    }
</style>
@endpush

@section('content')
<div class="midtrans-wait-page">
    <section class="midtrans-wait-card">
        <div class="midtrans-wait-icon"><i class="bi bi-credit-card"></i></div>
        <h1>Pembayaran Non Tunai</h1>
        <p id="midtransStatusText">Popup Midtrans sedang dibuka untuk invoice #{{ $order->invoice }}.</p>

        <div class="midtrans-total">
            <span>Total Pembayaran</span>
            <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
        </div>

        <div class="midtrans-actions">
            <button type="button" class="midtrans-primary" id="openSnap">
                <i class="bi bi-box-arrow-up-right"></i> Buka Pembayaran
            </button>
            <a class="midtrans-secondary" href="{{ route('payments.qris', $order) }}">
                <i class="bi bi-qr-code-scan"></i> Halaman QRIS
            </a>
            <a class="midtrans-secondary" href="{{ route('cashier.index') }}">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ config('midtrans.isProduction') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
const snapToken = @json($order->snap_token);
const qrisUrl = @json(route('payments.qris', $order));
const recapUrl = @json(route('payments.recap', $order));
const statusUrl = @json(route('order.success.status', $order));
const statusText = document.getElementById('midtransStatusText');
const openSnap = document.getElementById('openSnap');
let redirected = false;

function redirectOnce(url) {
    if (redirected) return;
    redirected = true;
    window.location.href = url;
}

function openMidtransPopup() {
    if (typeof snap === 'undefined') {
        statusText.textContent = 'Snap Midtrans gagal dimuat. Coba muat ulang halaman.';
        return;
    }

    snap.pay(snapToken, {
        onSuccess: function () {
            statusText.textContent = 'Pembayaran berhasil. Membuka rekap pesanan...';
            redirectOnce(recapUrl);
        },
        onPending: function () {
            statusText.textContent = 'Pembayaran menunggu penyelesaian. Membuka halaman QRIS...';
            redirectOnce(qrisUrl);
        },
        onError: function () {
            statusText.textContent = 'Pembayaran gagal. Silakan buka pembayaran lagi.';
        },
        onClose: function () {
            statusText.textContent = 'Popup ditutup. Tekan Buka Pembayaran untuk melanjutkan.';
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
            redirectOnce(recapUrl);
        }
    } catch (error) {
        console.log('Gagal mengecek pembayaran:', error);
    }
}

openSnap.addEventListener('click', openMidtransPopup);
setInterval(pollPaymentStatus, 3000);

@if(request()->boolean('auto_pay'))
window.addEventListener('load', function () {
    setTimeout(openMidtransPopup, 500);
});
@endif
</script>
@endpush
