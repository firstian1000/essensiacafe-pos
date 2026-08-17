@extends('layouts.customer')

@section('content')
<section class="success-v2">
    <div class="success-v2-wrap">
        <div class="success-v2-hero">
            <div class="success-check"><i class="bi bi-qr-code-scan"></i></div>
            <h1>Menunggu Pembayaran</h1>
            <p>Selesaikan pembayaran melalui Midtrans.<br>Halaman ini akan otomatis berpindah setelah pembayaran berhasil.</p>
        </div>

        <div class="success-summary-bar">
            <div class="summary-item">
                <i class="bi bi-receipt"></i>
                <div>
                    <span>Nomor Invoice</span>
                    <strong>{{ $order->invoice }}</strong>
                </div>
            </div>
            <div class="summary-item">
                <i class="bi bi-display"></i>
                <div>
                    <span>Meja</span>
                    <strong>{{ optional($order->table)->display_name ?? '-' }}</strong>
                </div>
            </div>
            <div class="summary-item payment">
                <i class="bi bi-credit-card"></i>
                <div>
                    <span>Total Pembayaran</span>
                    <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="tracking-banner">
            <div>
                <strong id="paymentStatusText">Popup pembayaran sedang dibuka.</strong>
                <p id="paymentStatusHelp">Jika popup tidak muncul, tekan tombol Buka Pembayaran.</p>
            </div>
            <button type="button" class="primary" id="pay-button" style="border:0;">
                <i class="bi bi-credit-card"></i> Buka Pembayaran
            </button>
        </div>

        <div class="success-bottom-actions">
            @if($order->table && $order->table->qr_token)
                <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="outline"><i class="bi bi-house-door"></i> Kembali ke Menu</a>
            @endif
            <a href="{{ route('order.success', $order) }}" class="outline"><i class="bi bi-clock-history"></i> Lihat Status Pesanan</a>
        </div>
    </div>
</section>

<script src="{{ config('midtrans.isProduction') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
localStorage.removeItem('essensia_customer_cart');

const snapToken = @json($order->snap_token);
const statusUrl = @json(route('order.success.status', $order));
const successUrl = @json(route('order.success', $order));
const payButton = document.getElementById('pay-button');
const statusText = document.getElementById('paymentStatusText');
const statusHelp = document.getElementById('paymentStatusHelp');
let redirected = false;

function goToSuccess() {
    if (redirected) return;
    redirected = true;
    statusText.textContent = 'Pembayaran berhasil.';
    statusHelp.textContent = 'Mengalihkan ke halaman status pesanan...';
    setTimeout(() => {
        window.location.href = successUrl;
    }, 900);
}

function openMidtransPayment() {
    if (typeof snap === 'undefined') {
        statusText.textContent = 'Snap Midtrans gagal dimuat.';
        statusHelp.textContent = 'Cek koneksi internet, lalu coba buka pembayaran lagi.';
        return;
    }

    snap.pay(snapToken, {
        onSuccess: goToSuccess,
        onPending: function () {
            statusText.textContent = 'Pembayaran sedang diproses.';
            statusHelp.textContent = 'Kami akan mengalihkan otomatis setelah pembayaran dikonfirmasi.';
        },
        onError: function () {
            statusText.textContent = 'Pembayaran gagal.';
            statusHelp.textContent = 'Silakan tekan Buka Pembayaran untuk mencoba lagi.';
        },
        onClose: function () {
            statusText.textContent = 'Popup pembayaran ditutup.';
            statusHelp.textContent = 'Pesanan belum lunas. Tekan Buka Pembayaran untuk melanjutkan.';
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
            goToSuccess();
        } else if (data.payment_status === 'failed' || data.payment_status === 'expired') {
            statusText.textContent = 'Pembayaran gagal atau kedaluwarsa.';
            statusHelp.textContent = 'Silakan buat pesanan ulang atau hubungi kasir.';
        }
    } catch (error) {
        console.log('Gagal mengecek pembayaran:', error);
    }
}

payButton.addEventListener('click', openMidtransPayment);
setInterval(pollPaymentStatus, 3000);

@if(request()->boolean('auto_pay'))
window.addEventListener('load', function () {
    setTimeout(openMidtransPayment, 500);
});
@endif
</script>
@endsection
