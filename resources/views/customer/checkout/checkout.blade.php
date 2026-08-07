@extends('layouts.customer')

@section('content')

<div class="checkout-page">

    {{-- ===== BACK LINK ===== --}}
    <a href="{{ route('cart.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
    </a>

    {{-- ===== HEADING ===== --}}
    <h1 class="checkout-heading">Checkout</h1>
    <p class="checkout-subheading">Lengkapi data sebelum melakukan pembayaran</p>

    @php
        $total = 0;
        $totalItem = 0;
    @endphp

    <form action="{{ route('checkout.store') }}" method="POST">
    @csrf

    <div class="checkout-layout">

        {{-- ========================= --}}
        {{-- ======= KIRI =========== --}}
        {{-- ========================= --}}
        <div class="checkout-left">

            {{-- === SECTION 1: Ringkasan Pesanan === --}}
            <div class="checkout-section-card">
                <h3 class="checkout-section-title">
                    <span class="section-num"><i class="bi bi-card-checklist"></i></span>
                    1. Ringkasan Pesanan
                </h3>

                @foreach($cart as $item)
                @php
                    $subtotal   = $item['price'] * $item['qty'];
                    $total     += $subtotal;
                    $totalItem += $item['qty'];
                    $categoryColors = [
                        'Coffee'      => 'badge-coffee',
                        'Non Coffee'  => 'badge-noncoffee',
                        'Milkbase'    => 'badge-milkbase',
                        'Main Course' => 'badge-maincourse',
                        'Snack'       => 'badge-snack',
                    ];
                    $badgeClass = $categoryColors[$item['category'] ?? ''] ?? 'badge-default';
                @endphp

                <div class="checkout-item-row">

                    {{-- Foto --}}
                    <div class="checkout-item-img-wrap">
                        <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('images/no-image.png') }}"
                             class="checkout-item-img"
                             alt="{{ $item['name'] }}">
                    </div>

                    {{-- Info --}}
                    <div class="checkout-item-info">
                        <div class="checkout-item-top">
                            <div>
                                <h4 class="checkout-item-name">{{ $item['name'] }}</h4>
                                @if(!empty($item['category']))
                                <span class="cat-badge {{ $badgeClass }}">{{ $item['category'] }}</span>
                                @endif
                                <div class="checkout-unit-price">
                                    Rp {{ number_format($item['price'],0,',','.') }}
                                    <span class="text-muted">Harga Satuan</span>
                                </div>
                            </div>
                            <div class="checkout-item-right">
                                <div class="checkout-subtotal-price">Rp {{ number_format($subtotal,0,',','.') }}</div>
                                <small class="text-muted">Subtotal</small>
                            </div>
                        </div>

                        <div class="checkout-item-controls">
                            {{-- QTY --}}
                            <div class="qty-box">
                                <a href="{{ route('cart.decrease', $item['id']) }}" class="qty-btn">
                                    <i class="bi bi-dash-lg"></i>
                                </a>
                                <span class="qty-number">{{ $item['qty'] }}</span>
                                <a href="{{ route('cart.increase', $item['id']) }}" class="qty-btn qty-plus">
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                            {{-- DELETE --}}
                            <a href="{{ route('cart.remove', $item['id']) }}"
                               class="delete-btn"
                               onclick="return confirm('Hapus menu ini?')">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
                        </div>
                    </div>

                </div>

                @if(!$loop->last)
                <div class="cart-item-divider"></div>
                @endif

                @endforeach

                {{-- Summary row --}}
                <div class="checkout-summary-row-mini">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Item</span>
                        <strong>{{ $totalItem }} Menu</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong class="checkout-subtotal-text">Rp {{ number_format($total,0,',','.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Biaya Layanan</span>
                        <strong class="text-success-custom">Gratis</strong>
                    </div>
                </div>
            </div>

            {{-- === SECTION 2: Data Pelanggan === --}}
            <div class="checkout-section-card">
                <h3 class="checkout-section-title">
                    <span class="section-num"><i class="bi bi-person-vcard"></i></span>
                    2.Data Pelanggan
                </h3>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Lengkap</label>
                    <div class="input-wrap-custom">
                        <i class="bi bi-person input-icon-custom"></i>
                        <input
                            type="text"
                            name="customer_name"
                            class="input-custom"
                            placeholder="Masukkan nama lengkap Anda"
                            required>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nomor WhatsApp</label>
                    <div class="input-wrap-custom">
                        <i class="bi bi-whatsapp input-icon-custom"></i>
                        <input
                            type="text"
                            name="phone"
                            class="input-custom"
                            placeholder="08xxxxxxxxxx">
                    </div>
                </div>

            </div>

            {{-- === SECTION 3: Metode Pembayaran === --}}
            <div class="checkout-section-card">
                <h3 class="checkout-section-title">
                    <span class="section-num"><i class="bi bi-credit-card"></i></span>
                    3.Metode Pembayaran
                </h3>

                {{-- Cash --}}
                <label class="payment-card" id="pay-cash-label">
                    <input type="radio" name="payment_method" value="cash" checked
                           onchange="selectPayment(this)" class="payment-radio">
                    <div class="payment-card-icon payment-icon-cash">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="payment-card-info">
                        <strong>Bayar di Kasir (Cash)</strong>
                        <span>Lakukan pembayaran secara langsung di kasir</span>
                    </div>
                    <div class="payment-card-check">
                        <i class="bi bi-circle payment-unchecked"></i>
                        <i class="bi bi-check-circle-fill payment-checked"></i>
                    </div>
                </label>

                {{-- QRIS --}}
                <label class="payment-card" id="pay-qris-label">
                    <input type="radio" name="payment_method" value="midtrans"
                           onchange="selectPayment(this)" class="payment-radio">
                    <div class="payment-card-icon payment-icon-qris">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="payment-card-info">
                        <strong>QRIS / Transfer Bank / E-Wallet (Midtrans)</strong>
                        <span>Pembayaran online via QRIS, transfer bank, atau e-wallet</span>
                    </div>
                    <div class="payment-card-check">
                        <i class="bi bi-circle payment-unchecked"></i>
                        <i class="bi bi-check-circle-fill payment-checked"></i>
                    </div>
                </label>

                {{-- Info Box --}}
                <div class="checkout-info-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <p class="mb-0">
                            Pesanan akan diproses setelah pembayaran berhasil.
                            Pastikan data yang Anda masukkan sudah benar.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- ========================= --}}
        {{-- ======= KANAN =========== --}}
        {{-- ========================= --}}
        <div class="checkout-right">

            <div class="summary-sticky">
                <div class="summary-card-new">

                    {{-- Header --}}
                    <h3 class="summary-title">
                        <i class="bi bi-receipt-cutoff"></i>
                        Detail Pembayaran
                    </h3>

                    {{-- Rows --}}
                    <div class="summary-row">
                        <span class="summary-label">Total Item</span>
                        <strong class="summary-value">{{ $totalItem }} Menu</strong>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <strong class="summary-value">Rp {{ number_format($total,0,',','.') }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Biaya Layanan</span>
                        <strong class="summary-value text-success-custom">Gratis</strong>
                    </div>

                    <div class="summary-divider"></div>

                    <p class="summary-total-label">Total Pembayaran</p>
                    <div class="summary-total-price">Rp {{ number_format($total,0,',','.') }}</div>

                    {{-- Meja --}}
                    <div class="summary-table-info">
                        <i class="bi bi-grid-3x3 summary-table-icon"></i>
                        <div>
                            <strong>{{ session('table_number') }}</strong>
                            <p>Pesanan akan diantar ke meja ini.</p>
                        </div>
                    </div>

                    {{-- Bayar --}}
                    @if(\App\Models\Setting::isOrderingClosed())
                    <button type="button" class="btn-bayar-sekarang" style="background-color: #6B7280 !important; cursor: not-allowed;" disabled>
                        <i class="bi bi-lock-fill me-2"></i>Pemesanan Ditutup
                    </button>
                    <div class="alert alert-danger mt-3 p-2 text-center" style="font-size: 0.8rem; border-radius: 8px; font-weight: 600;">
                        Pemesanan sudah ditutup untuk hari ini (melewati batas operasional).
                    </div>
                    @else
                    <button type="submit" class="btn-bayar-sekarang">
                        <i class="bi bi-lock-fill me-2"></i>Bayar Sekarang
                    </button>
                    @endif

                    <p class="checkout-terms">
                        Dengan melakukan pembayaran, Anda menyetujui
                        <a href="#">syarat &amp; ketentuan</a> yang berlaku.
                    </p>

                    {{-- Fitur --}}
                    <div class="summary-features">
                        <div class="summary-feature-item">
                            <i class="bi bi-shield-check text-success"></i>
                            <div>
                                <strong>Aman</strong>
                                <span>Pembayaran terjamin aman</span>
                            </div>
                        </div>
                        <div class="summary-feature-item">
                            <i class="bi bi-lightning-charge text-warning"></i>
                            <div>
                                <strong>Cepat</strong>
                                <span>Proses pesanan lebih cepat</span>
                            </div>
                        </div>
                        <div class="summary-feature-item">
                            <i class="bi bi-patch-check text-primary-custom"></i>
                            <div>
                                <strong>Terjamin</strong>
                                <span>Kualitas terbaik untuk Anda</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
function selectPayment(radio) {
    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('selected');
    });
    radio.closest('.payment-card').classList.add('selected');
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('.payment-radio:checked');
    if (checked) selectPayment(checked);
});
</script>
@endpush
