@extends('layouts.customer')

@section('content')
@php
    $subtotal = $order->items->sum('subtotal');
    $methodLabel = $order->payment_method === 'cash' ? 'Tunai' : 'Non Tunai (Midtrans)';
    $paymentStatusLabel = $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran';
    $serviceLabel = ($order->service_type ?? 'dine_in') === 'take_away' ? 'Take Away' : 'Dine In';
    $tableName = optional($order->table)->display_name ?? '-';
    $isCancelled = $order->status === 'cancelled';
    $steps = [
        [1, 'Pesanan Diterima', $order->created_at->format('d M Y - H:i'), true],
        [2, 'Sedang Diproses', 'Dapur/bar sedang membuat pesanan', in_array($order->status, ['processing', 'completed'])],
        [3, 'Pesanan Sampai / Selesai', 'Terima kasih, selamat menikmati!', $order->status === 'completed'],
    ];
@endphp

<section class="success-v2">
    <div class="success-v2-wrap">
        @if($isCancelled)
        <div class="success-v2-hero" style="background: linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 100%); border-color: #FECDD3;">
            <div class="success-check" style="background: #E11D48; color: #fff;"><i class="bi bi-x-lg"></i></div>
            <h1 style="color: #BE123C;">Pesanan Dibatalkan</h1>
            <p style="color: #9F1239;">Mohon maaf, pesanan ini telah dibatalkan.<br>Silakan tanyakan kepada staf/kasir jika memerlukan bantuan.</p>
        </div>
        @else
        <div class="success-v2-hero">
            <div class="confetti-dot dot-a"></div>
            <div class="confetti-dot dot-b"></div>
            <div class="confetti-dot dot-c"></div>
            <div class="success-check"><i class="bi bi-check-lg"></i></div>
            <h1>Pesanan Berhasil!</h1>
            <p>Terima kasih, pesanan Anda sudah kami terima.<br>Kami akan segera memprosesnya.</p>
        </div>
        @endif

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
                    <strong>{{ $tableName }}</strong>
                </div>
            </div>
            <div class="summary-item">
                <i class="bi bi-clock"></i>
                <div>
                    <span>Waktu Pesan</span>
                    <strong>{{ $order->created_at->format('d M Y - H:i') }}</strong>
                </div>
            </div>
            <div class="summary-item payment">
                <i class="bi bi-cash-stack"></i>
                <div>
                    <span>Metode Pembayaran</span>
                    <strong id="successPaymentLabel" data-method-label="{{ $methodLabel }}">{{ $methodLabel }} ({{ $paymentStatusLabel }})</strong>
                </div>
            </div>
            <div class="summary-item">
                <i class="bi {{ $serviceLabel === 'Take Away' ? 'bi-bag-check' : 'bi-cup-hot' }}"></i>
                <div>
                    <span>Layanan</span>
                    <strong>{{ $serviceLabel }}</strong>
                </div>
            </div>
        </div>

        <div class="success-content-grid">
            <div class="success-order-card">
                <div class="success-section-title">
                    <i class="bi bi-receipt"></i>
                    <h2>Detail Pesanan</h2>
                </div>

                <div class="success-items">
                    <div class="success-items-head">
                        <span>Menu</span>
                        <span>Harga</span>
                        <span>Qty</span>
                        <span>Subtotal</span>
                    </div>
                    @foreach($order->items as $item)
                        @php
                            $menu = $item->menu;
                            $image = $menu?->image ? asset('storage/' . $menu->image) : asset('images/no-image.png');
                            $categoryName = strtolower(trim($menu?->category?->name ?? ''));
                            $disableDrinkOptions = in_array($categoryName, ['snack', 'snacks', 'dimsum', 'main course', 'main cource', 'add on', 'addon'], true);
                        @endphp
                        <div class="success-item-row">
                            <div class="success-menu-cell">
                                <img src="{{ $image }}" alt="{{ $menu?->name ?? 'Menu' }}">
                                <div>
                                    <strong>{{ $menu?->name ?? 'Menu dihapus' }}</strong>
                                    @if($item->variant_name)
                                        <span>Varian: {{ $item->variant_name }}</span>
                                    @endif
                                    <span>{{ $menu?->category?->name ?? 'Menu' }}</span>
                                    <small>
                                        @unless($disableDrinkOptions)
                                            Sugar: {{ ucfirst($item->sugar_level ?? 'normal') }},
                                            Temperature: {{ ucfirst($item->temperature ?? 'ice') }},
                                            Ice: {{ ($item->temperature ?? 'ice') === 'hot' ? '-' : ucfirst($item->ice_level ?? 'normal') }}
                                        @endunless
                                        @if($item->add_on)
                                            @unless($disableDrinkOptions)<br>@endunless
                                            Add On: {{ $item->add_on }} (+Rp {{ number_format($item->add_on_price ?? 0, 0, ',', '.') }})
                                        @endif
                                        @if($item->note)
                                            <br>Catatan: {{ $item->note }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div>Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                            <div>{{ $item->qty }}</div>
                            <div class="success-subtotal">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="success-total-box">
                    <div><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    <div><span>Biaya Layanan</span><strong class="free">Gratis</strong></div>
                    <div class="grand"><span>Total Pembayaran</span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
                </div>
            </div>

            <aside class="success-side">
                <div class="status-card">
                    <h3>Status Pesanan</h3>
                    <div class="status-steps">
                        @foreach($steps as $index => $step)
                            <div class="status-step {{ $step[3] ? 'active' : '' }} {{ $step[0] === 2 ? 'process-step' : '' }} {{ $step[0] === 3 ? 'done-step' : '' }}" data-status-step="{{ $step[0] }}">
                                <span>{{ $step[3] && $index === 0 ? '' : $step[0] }}</span>
                                <div>
                                    <strong>{{ $step[1] }}</strong>
                                    <small>{{ $step[2] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="delivery-card">
                    <i class="bi bi-scooter"></i>
                    <div>
                        <span>{{ $serviceLabel === 'Take Away' ? 'Pesanan untuk' : 'Pesanan akan diantar ke' }}</span>
                        <strong>{{ $serviceLabel === 'Take Away' ? 'Take Away' : $tableName }}</strong>
                        <p>{{ $serviceLabel === 'Take Away' ? 'Silakan ambil pesanan Anda saat sudah siap.' : 'Silakan menunggu, pesanan Anda akan segera kami antar.' }}</p>
                    </div>
                </div>
            </aside>
        </div>

        <div class="tracking-banner no-action">
            <div>
                <strong>Ingin memantau pesanan secara real-time?</strong>
                <p>Anda bisa melihat status pesanan kapan saja di halaman ini.</p>
            </div>
        </div>

        @if($order->table && $order->table->qr_token)
        <div class="success-bottom-actions">
            <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="outline"><i class="bi bi-house-door"></i> Kembali ke Menu</a>
            <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="primary"><i class="bi bi-cart-plus"></i> Pesan Lagi</a>
        </div>
        @else
        <div class="success-bottom-actions">
            <a href="{{ route('cashier.index') }}" class="primary"><i class="bi bi-shop"></i> Kembali ke Kasir</a>
            @if($order->payment_method == 'midtrans' && $order->snap_token)
                <button type="button" id="pay-button" class="primary" style="border:0;">
                    <i class="bi bi-credit-card"></i> Buka Pembayaran
                </button>
            @endif
        </div>
        @endif
    </div>
</section>

@if($order->payment_method == 'midtrans' && $order->snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
@endif
<script>
localStorage.removeItem('essensia_customer_cart');

(function () {
    const statusUrl = @json(route('order.success.status', $order));
    const stepOne = document.querySelector('[data-status-step="1"]');
    const stepTwo = document.querySelector('[data-status-step="2"]');
    const stepThree = document.querySelector('[data-status-step="3"]');
    const paymentLabel = document.getElementById('successPaymentLabel');

    function setStepActive(step, active) {
        if (!step) return;
        step.classList.toggle('active', active);
        const number = step.querySelector('span');
        if (number && step.dataset.statusStep !== '1') {
            number.textContent = step.dataset.statusStep;
        }
    }

    async function refreshOrderStatus() {
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
            setStepActive(stepOne, true);
            setStepActive(stepTwo, ['processing', 'completed'].includes(data.status));
            setStepActive(stepThree, data.status === 'completed');

            if (paymentLabel) {
                const methodLabel = paymentLabel.dataset.methodLabel || 'Tunai';
                const statusLabel = data.payment_status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran';
                paymentLabel.textContent = `${methodLabel} (${statusLabel})`;
            }

            if (data.status === 'cancelled') {
                window.location.reload();
            }
        } catch (error) {
            console.log('Gagal update status pesanan:', error);
        }
    }

    setInterval(refreshOrderStatus, 3000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshOrderStatus();
        }
    });
})();
</script>
@if($order->payment_method == 'midtrans' && $order->snap_token)
<script>
const payButton = document.getElementById('pay-button');
function openMidtransPayment() {
    if (typeof snap === 'undefined') return;
    snap.pay('{{ $order->snap_token }}', {
        onSuccess: function() { location.reload(); },
        onPending: function() { alert("Menunggu pembayaran"); },
        onError: function() { alert("Pembayaran gagal"); },
        onClose: function() { alert("Popup pembayaran ditutup."); }
    });
}

if (payButton) {
    payButton.addEventListener('click', openMidtransPayment);
}

@if(request()->boolean('auto_pay'))
window.addEventListener('load', function () {
    setTimeout(openMidtransPayment, 500);
});
@endif
</script>
@endif
@endsection
