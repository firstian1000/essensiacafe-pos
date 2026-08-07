@extends('layouts.customer')

@section('content')

<div class="cart-page">

    {{-- ===== BACK LINK ===== --}}
    <a href="{{ route('customer.menu', $token) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke Menu
    </a>

    {{-- ===== PAGE HEADING ===== --}}
    <h1 class="cart-heading">Keranjang Belanja</h1>
    <p class="cart-subheading">Periksa kembali pesanan sebelum checkout</p>

    {{-- ===== FLASH ===== --}}
    @if(session('success'))
    <div class="alert-custom alert-success-custom">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-custom alert-danger-custom">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- ===== EMPTY STATE ===== --}}
    @if(count($cart) == 0)

    <div class="cart-empty">
        <div class="cart-empty-icon">
            <i class="bi bi-cart-x"></i>
        </div>
        <h3>Keranjang Masih Kosong</h3>
        <p>Yuk pilih menu favoritmu.</p>
        <a href="{{ route('customer.menu', $token) }}" class="btn-go-menu">
            <i class="bi bi-cup-hot me-2"></i> Mulai Pesan
        </a>
    </div>

    @else

    @php
        $total = 0;
    @endphp

    <div class="cart-layout">

        {{-- ========== KIRI ========== --}}
        <div class="cart-left">

            {{-- Items --}}
            <div class="cart-items-card">

                @foreach($cart as $item)
                @php
                    $subtotal = $item['price'] * $item['qty'];
                    $total += $subtotal;

                    $categoryColors = [
                        'Coffee'      => 'badge-coffee',
                        'Non Coffee'  => 'badge-noncoffee',
                        'Milkbase'    => 'badge-milkbase',
                        'Main Course' => 'badge-maincourse',
                        'Snack'       => 'badge-snack',
                    ];
                    $badgeClass = $categoryColors[$item['category'] ?? ''] ?? 'badge-default';
                @endphp

                <div class="cart-item-row">

                    {{-- Foto --}}
                    <div class="cart-item-img-wrap">
                        @if(!empty($item['image']))
                        <img src="{{ asset('storage/'.$item['image']) }}"
                             class="cart-item-img"
                             alt="{{ $item['name'] }}">
                        @else
                        <img src="{{ asset('images/no-image.png') }}"
                             class="cart-item-img"
                             alt="{{ $item['name'] }}">
                        @endif
                        {{-- Heart --}}
                        <button class="cart-item-wish" aria-label="Favorit">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>

                    {{-- Info --}}
                    <div class="cart-item-info">

                        <div class="cart-item-top">
                            <div>
                                <h4 class="cart-item-name">{{ $item['name'] }}</h4>
                                @if(!empty($item['category']))
                                <span class="cat-badge {{ $badgeClass }}">{{ $item['category'] }}</span>
                                @endif
                            </div>
                            <div class="cart-item-subtotal">
                                <div class="cart-item-price-red">Rp {{ number_format($subtotal,0,',','.') }}</div>
                                <small class="text-muted">Subtotal</small>
                            </div>
                        </div>

                        <div class="cart-item-bottom">
                            <div class="cart-item-unit">
                                <span class="cart-item-unit-label">Harga Satuan</span>
                                <span class="cart-item-unit-price">Rp {{ number_format($item['price'],0,',','.') }}</span>
                            </div>
                            <div class="cart-item-actions">
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
                                   onclick="return confirm('Hapus menu ini dari keranjang?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

                @if(!$loop->last)
                <div class="cart-item-divider"></div>
                @endif

                @endforeach

            </div>

            {{-- Catatan --}}
            <div class="cart-note-box">
                <i class="bi bi-info-circle-fill cart-note-icon"></i>
                <div>
                    <strong class="cart-note-title">Catatan</strong>
                    <p class="cart-note-text">Pesanan akan diantar ke meja Anda.</p>
                </div>
            </div>

        </div>

        {{-- ========== KANAN ========== --}}
        <div class="cart-right">

            <div class="summary-sticky">

                <div class="summary-card-new">

                    {{-- Header --}}
                    <h3 class="summary-title">
                        <i class="bi bi-receipt-cutoff"></i>
                        Ringkasan Pesanan
                    </h3>

                    {{-- Detail --}}
                    <div class="summary-row">
                        <span class="summary-label">Total Menu</span>
                        <strong class="summary-value">{{ count($cart) }}</strong>
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

                    {{-- Total --}}
                    <p class="summary-total-label">Total Pembayaran</p>
                    <div class="summary-total-price">Rp {{ number_format($total,0,',','.') }}</div>

                    {{-- Meja --}}
                    <div class="summary-table-info">
                        <i class="bi bi-grid-3x3 summary-table-icon"></i>
                        <div>
                            <strong>Nomor Meja : {{ session('table_number') }}</strong>
                            <p>Pesanan akan diantar ke meja ini.</p>
                        </div>
                    </div>

                    {{-- Checkout --}}
                    <a href="{{ route('checkout.index') }}" class="btn-checkout-now">
                        Checkout Sekarang <i class="bi bi-arrow-right"></i>
                    </a>

                    <div class="summary-or">
                        <span>atau</span>
                    </div>

                    {{-- Kosongkan --}}
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Yakin ingin mengosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-clear-cart">
                            <i class="bi bi-trash3 me-2"></i>Kosongkan Keranjang
                        </button>
                    </form>

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

    {{-- ===== REKOMENDASI ===== --}}
    @php
        $rekomendasi = $recommendations ?? collect();
    @endphp
    @if($rekomendasi->count() > 0)
    <div class="reko-section">
        <h2 class="reko-title">
            <i class="bi bi-stars"></i> Rekomendasi Untukmu
        </h2>
        <div class="reko-grid">
            @foreach($rekomendasi as $reko)
            @php
                $categoryColors = [
                    'Coffee'      => 'badge-coffee',
                    'Non Coffee'  => 'badge-noncoffee',
                    'Milkbase'    => 'badge-milkbase',
                    'Main Course' => 'badge-maincourse',
                    'Snack'       => 'badge-snack',
                ];
                $badgeClass = $categoryColors[$reko->category->name ?? ''] ?? 'badge-default';
            @endphp
            <div class="reko-card">
                <div class="reko-img-wrap">
                    @if($reko->image)
                    <img src="{{ asset('storage/'.$reko->image) }}" class="reko-img" alt="{{ $reko->name }}">
                    @else
                    <img src="{{ asset('images/no-image.png') }}" class="reko-img" alt="{{ $reko->name }}">
                    @endif
                    <button class="wish-btn" aria-label="Favorit">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
                <div class="reko-body">
                    <span class="cat-badge {{ $badgeClass }}">{{ $reko->category->name ?? '' }}</span>
                    <h4 class="reko-name">{{ $reko->name }}</h4>
                    <div class="reko-price">Rp {{ number_format($reko->price,0,',','.') }}</div>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $reko->id }}">
                        <button class="btn-add-cart w-100">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif

</div>

@endsection

@push('scripts')
<script>
// Wish button toggle
document.querySelectorAll('.wish-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-heart');
        icon.classList.toggle('bi-heart-fill');
        this.classList.toggle('wished');
    });
});
</script>
@endpush
