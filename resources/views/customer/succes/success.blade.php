@extends('layouts.customer')

@section('content')
@php
    $subtotal = $order->items->sum('subtotal');
    $methodLabel = $order->payment_method === 'cash' ? 'Cash' : 'QRIS (Midtrans)';
    $tableName = optional($order->table)->display_name ?? '-';
    $steps = [
        [1, 'Pesanan Diterima', $order->created_at->format('d M Y - H:i'), true],
        [2, 'Sedang Diproses', 'Menunggu konfirmasi kitchen', in_array($order->status, ['processing', 'completed'])],
        [3, 'Selesai', 'Terima kasih!', $order->status === 'completed'],
    ];
@endphp

<section class="success-v2">
    <div class="success-v2-wrap">
        <div class="success-v2-hero">
            <div class="confetti-dot dot-a"></div>
            <div class="confetti-dot dot-b"></div>
            <div class="confetti-dot dot-c"></div>
            <div class="success-check"><i class="bi bi-check-lg"></i></div>
            <h1>Pesanan Berhasil!</h1>
            <p>Terima kasih, pesanan Anda sudah kami terima.<br>Kami akan segera memprosesnya.</p>
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
                    <strong>{{ $methodLabel }}</strong>
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
                        @endphp
                        <div class="success-item-row">
                            <div class="success-menu-cell">
                                <img src="{{ $image }}" alt="{{ $menu?->name ?? 'Menu' }}">
                                <div>
                                    <strong>{{ $menu?->name ?? 'Menu dihapus' }}</strong>
                                    <span>{{ $menu?->category?->name ?? 'Menu' }}</span>
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
                            <div class="status-step {{ $step[3] ? 'active' : '' }} {{ $step[0] === 3 ? 'done-step' : '' }}">
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
                        <span>Pesanan akan diantar ke</span>
                        <strong>{{ $tableName }}</strong>
                        <p>Silakan menunggu, pesanan Anda akan segera kami antar.</p>
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

        <div class="success-bottom-actions">
            <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="outline"><i class="bi bi-house-door"></i> Kembali ke Menu</a>
            <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="primary"><i class="bi bi-cart-plus"></i> Pesan Lagi</a>
        </div>
    </div>
</section>

@if($order->payment_method == 'midtrans')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
const payButton = document.getElementById('pay-button');
if (payButton) {
    payButton.addEventListener('click', function () {
        snap.pay('{{ $order->snap_token }}', {
            onSuccess: function() { location.reload(); },
            onPending: function() { alert("Menunggu pembayaran"); },
            onError: function() { alert("Pembayaran gagal"); },
            onClose: function() { alert("Popup pembayaran ditutup."); }
        });
    });
}
</script>
@endif
@endsection
