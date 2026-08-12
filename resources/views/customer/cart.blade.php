@extends('layouts.customer')

@push('head')
<style>
.cart-items-card .cart-item-row-modern {
    display: grid !important;
    grid-template-columns: 132px minmax(0, 1fr) 172px !important;
    gap: 24px !important;
    align-items: start !important;
    padding: 18px 0 !important;
}
.cart-item-row-modern .cart-item-img-wrap,
.cart-item-row-modern .cart-item-img {
    width: 132px !important;
    height: 132px !important;
}
.cart-item-row-modern .cart-item-img {
    border-radius: 16px !important;
    object-fit: cover !important;
}
.cart-item-info-modern {
    display: block !important;
    min-width: 0 !important;
}
.cart-item-info-modern .cart-item-top {
    display: block !important;
    margin: 0 0 10px !important;
}
.cart-item-info-modern .cart-item-name {
    margin: 0 0 8px !important;
    font-size: 18px !important;
    line-height: 1.2 !important;
}
.cart-item-unit-modern {
    display: grid !important;
    gap: 2px !important;
    margin-top: 10px !important;
}
.cart-item-unit-modern .cart-item-unit-label {
    color: #64748B !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.cart-item-unit-modern .cart-item-unit-price {
    color: #0F172A !important;
    font-size: 15px !important;
    font-weight: 900 !important;
}
.cart-item-row-modern .cart-item-options {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 9px !important;
    max-width: 560px !important;
    margin-top: 8px !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
.cart-item-row-modern .option-field {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    min-width: 0 !important;
}
.cart-item-row-modern .option-field span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    color: #334155 !important;
    font-size: 11px !important;
    font-weight: 900 !important;
}
.cart-item-row-modern .option-field span i {
    color: #2E7DB8 !important;
    font-size: 12px !important;
}
.cart-item-row-modern .cart-item-options select,
.cart-item-row-modern .cart-item-options input {
    appearance: none !important;
    width: 100% !important;
    height: 36px !important;
    border: 1px solid #DDE7F1 !important;
    border-radius: 10px !important;
    background-color: #fff !important;
    color: #0F172A !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    padding: 0 30px 0 10px !important;
    outline: none !important;
    box-shadow: none !important;
}
.cart-item-row-modern .cart-item-options input {
    padding-right: 10px !important;
    font-weight: 700 !important;
}
.cart-item-row-modern .cart-item-options select {
    background-image: linear-gradient(45deg, transparent 50%, #2E7DB8 50%), linear-gradient(135deg, #2E7DB8 50%, transparent 50%) !important;
    background-position: calc(100% - 15px) 15px, calc(100% - 9px) 15px !important;
    background-size: 6px 6px, 6px 6px !important;
    background-repeat: no-repeat !important;
}
.cart-item-row-modern .option-field.is-disabled {
    opacity: .48 !important;
}
.cart-item-row-modern .option-field.is-disabled span,
.cart-item-row-modern .option-field.is-disabled span i {
    color: #94A3B8 !important;
}
.cart-item-row-modern .cart-item-options select:disabled {
    cursor: not-allowed !important;
    background-color: #F1F5F9 !important;
    color: #94A3B8 !important;
    background-image: none !important;
}
.cart-item-row-modern .cart-item-add-on,
.cart-item-row-modern .cart-item-note {
    grid-column: 1 / -1 !important;
}
.cart-item-row-modern .cart-options-status {
    grid-column: 1 / -1 !important;
    min-height: 14px !important;
    margin-top: -4px !important;
    color: #168957 !important;
    font-size: 11px !important;
    font-weight: 900 !important;
}
.cart-item-side {
    min-height: 132px !important;
    padding-left: 24px !important;
    border-left: 1px dashed #DDE7F1 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-end !important;
    justify-content: space-between !important;
    gap: 16px !important;
}
.cart-item-side .cart-item-subtotal {
    text-align: right !important;
}
.cart-item-side .cart-item-price-red {
    color: #8B4A21 !important;
    font-size: 18px !important;
    font-weight: 950 !important;
}
.cart-item-side .cart-item-actions {
    width: 100% !important;
    display: flex !important;
    justify-content: flex-end !important;
    gap: 12px !important;
}
.service-type-box {
    margin: 18px 0 !important;
    padding: 14px !important;
    border: 1px solid #DDE7F1 !important;
    border-radius: 14px !important;
    background: #F8FBFF !important;
}
.service-type-title {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 10px !important;
    color: #0F172A !important;
    font-size: 14px !important;
    font-weight: 950 !important;
}
.service-type-title i {
    color: #2E7DB8 !important;
}
.service-type-options {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 10px !important;
}
.service-type-option {
    position: relative !important;
    cursor: pointer !important;
}
.service-type-option input {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
}
.service-type-card {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    min-height: 46px !important;
    border: 1px solid #DDE7F1 !important;
    border-radius: 12px !important;
    background: #fff !important;
    color: #475569 !important;
    font-size: 13px !important;
    font-weight: 950 !important;
    transition: .18s ease !important;
}
.service-type-card i {
    font-size: 16px !important;
}
.service-type-option input:checked + .service-type-card {
    border-color: #2E7DB8 !important;
    background: #EAF5FF !important;
    color: #1D6FA8 !important;
    box-shadow: 0 10px 22px rgba(46, 125, 184, .16) !important;
}
@media (max-width: 980px) {
    .cart-items-card .cart-item-row-modern {
        grid-template-columns: 110px minmax(0, 1fr) !important;
    }
    .cart-item-row-modern .cart-item-img-wrap,
    .cart-item-row-modern .cart-item-img {
        width: 110px !important;
        height: 110px !important;
    }
    .cart-item-side {
        grid-column: 1 / -1 !important;
        min-height: 0 !important;
        padding: 14px 0 0 !important;
        border-left: 0 !important;
        border-top: 1px dashed #DDE7F1 !important;
        flex-direction: row !important;
        align-items: center !important;
    }
}
@media (max-width: 620px) {
    .cart-items-card .cart-item-row-modern {
        grid-template-columns: 88px minmax(0, 1fr) !important;
        gap: 14px !important;
    }
    .cart-item-row-modern .cart-item-img-wrap,
    .cart-item-row-modern .cart-item-img {
        width: 88px !important;
        height: 88px !important;
    }
    .cart-item-row-modern .cart-item-options {
        grid-template-columns: 1fr !important;
    }
    .cart-item-side {
        flex-direction: column !important;
        align-items: stretch !important;
    }
}
</style>
@endpush

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

    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        const backup = localStorage.getItem('essensia_customer_cart');
        if (!backup) return;

        try {
            const cart = JSON.parse(backup);
            if (!Array.isArray(cart) || cart.length === 0) return;

            const response = await fetch(@json(route('cart.restore')), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ cart }),
            });

            const data = await response.json();
            if (response.ok && data.success) {
                window.location.reload();
            }
        } catch (error) {
            localStorage.removeItem('essensia_customer_cart');
        }
    });
    </script>

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
                    $allowIce = $item['allow_ice'] ?? true;
                    $allowHot = $item['allow_hot'] ?? true;
                    $categoryName = strtolower(trim($item['category'] ?? ''));
                    $canUseAddOn = in_array($categoryName, ['main course', 'main cource'], true);
                    $addOnPrice = $canUseAddOn ? (int) ($item['add_on_price'] ?? 0) : 0;
                    $unitTotal = $item['price'] + $addOnPrice;
                    $subtotal = $unitTotal * $item['qty'];
                    $disableDrinkOptions = in_array($categoryName, ['snack', 'snacks', 'dimsum', 'main course', 'main cource', 'add on', 'addon'], true);
                    $variantMenu = $variantMenus[$item['id']] ?? null;
                    $activeVariants = $variantMenu?->variants ?? collect();
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

                <div class="cart-item-row cart-item-row-modern">

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
                    <div class="cart-item-info cart-item-info-modern">

                        <div class="cart-item-top">
                            <div>
                                <h4 class="cart-item-name">{{ $item['name'] }}</h4>
                                @if(!empty($item['category']))
                                <span class="cat-badge {{ $badgeClass }}">{{ $item['category'] }}</span>
                                @endif
                                <div class="cart-item-unit cart-item-unit-modern">
                                    <span class="cart-item-unit-label">Harga Satuan</span>
                                    <span class="cart-item-unit-price">Rp {{ number_format($item['price'],0,',','.') }}</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('cart.options', $item['cart_key'] ?? $item['id']) }}" method="POST" class="cart-item-options js-cart-options-form">
                            @csrf
                            @method('PATCH')
                            @if($activeVariants->count() > 0)
                                <label class="option-field">
                                    <span><i class="bi bi-layers"></i> Varian</span>
                                    <select name="variant_id">
                                        @foreach($activeVariants as $variant)
                                            <option value="{{ $variant->id }}" {{ (string)($item['variant_id'] ?? '') === (string)$variant->id ? 'selected' : '' }}>
                                                {{ $variant->name }} - Rp {{ number_format($variant->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
                            @if($disableDrinkOptions)
                                <input type="hidden" name="sugar_level" value="normal">
                                <input type="hidden" name="temperature" value="ice">
                                <input type="hidden" name="ice_level" value="normal">
                            @else
                                <label class="option-field">
                                    <span><i class="bi bi-cup-straw"></i> Sugar</span>
                                    <select name="sugar_level">
                                        <option value="normal" {{ ($item['sugar_level'] ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="less" {{ ($item['sugar_level'] ?? 'normal') === 'less' ? 'selected' : '' }}>Less</option>
                                        <option value="no" {{ ($item['sugar_level'] ?? 'normal') === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </label>
                                <label class="option-field">
                                    <span><i class="bi bi-thermometer-half"></i> Temperature</span>
                                    <select name="temperature" class="js-temperature-select">
                                        @if($allowIce)
                                        <option value="ice" {{ ($item['temperature'] ?? 'ice') === 'ice' ? 'selected' : '' }}>Ice</option>
                                        @endif
                                        @if($allowHot)
                                        <option value="hot" {{ ($item['temperature'] ?? 'ice') === 'hot' ? 'selected' : '' }}>Hot</option>
                                        @endif
                                    </select>
                                </label>
                                <label class="option-field js-ice-field">
                                    <span><i class="bi bi-snow2"></i> Ice</span>
                                    <select name="ice_level" class="js-ice-select">
                                        <option value="normal" {{ ($item['ice_level'] ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="less" {{ ($item['ice_level'] ?? 'normal') === 'less' ? 'selected' : '' }}>Less</option>
                                    </select>
                                </label>
                            @endif
                            @if($canUseAddOn)
                                <label class="cart-item-add-on option-field">
                                    <span><i class="bi bi-plus-square"></i> Add On</span>
                                    <select name="add_on_menu_id">
                                        <option value="">Tanpa Add On</option>
                                        @foreach($addOns as $addOn)
                                            <option value="{{ $addOn->id }}" {{ (string)($item['add_on_menu_id'] ?? '') === (string)$addOn->id ? 'selected' : '' }}>
                                                {{ $addOn->name }} - Rp {{ number_format($addOn->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            @else
                                <input type="hidden" name="add_on_menu_id" value="">
                            @endif
                            <label class="cart-item-note option-field">
                                <span><i class="bi bi-pencil-square"></i> Catatan</span>
                                <input type="text" name="note" value="{{ $item['note'] ?? '' }}" placeholder="Contoh: tanpa topping, pisah saus">
                            </label>
                            <small class="cart-options-status" aria-live="polite"></small>
                        </form>

                    </div>

                    <div class="cart-item-side">
                        <div class="cart-item-subtotal">
                            <div class="cart-item-price-red">Rp {{ number_format($subtotal,0,',','.') }}</div>
                            <small class="text-muted">Subtotal</small>
                        </div>

                        <div class="cart-item-actions">
                            {{-- QTY --}}
                            <div class="qty-box">
                                <a href="{{ route('cart.decrease', $item['cart_key'] ?? $item['id']) }}" class="qty-btn js-cart-action" data-cart-action="decrease" data-cart-key="{{ $item['cart_key'] ?? $item['id'] }}" aria-label="Kurangi jumlah {{ $item['name'] }}">
                                    <i class="bi bi-dash-lg"></i>
                                </a>
                                <span class="qty-number">{{ $item['qty'] }}</span>
                                <a href="{{ route('cart.increase', $item['cart_key'] ?? $item['id']) }}" class="qty-btn qty-plus js-cart-action" data-cart-action="increase" data-cart-key="{{ $item['cart_key'] ?? $item['id'] }}" aria-label="Tambah jumlah {{ $item['name'] }}">
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                            {{-- DELETE --}}
                            <a href="{{ route('cart.remove', $item['cart_key'] ?? $item['id']) }}"
                               class="delete-btn js-cart-action"
                               data-cart-action="remove"
                               data-cart-key="{{ $item['cart_key'] ?? $item['id'] }}"
                               data-confirm-message="Hapus menu ini dari keranjang?"
                               aria-label="Hapus {{ $item['name'] }} dari keranjang">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
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

                    @php
                        $serviceType = session('service_type', 'dine_in');
                    @endphp
                    <form action="{{ route('cart.service-type') }}" method="POST" class="service-type-box js-service-type-form">
                        @csrf
                        @method('PATCH')
                        <div class="service-type-title">
                            <i class="bi bi-bag-check"></i>
                            Pilihan Layanan
                        </div>
                        <div class="service-type-options">
                            <label class="service-type-option">
                                <input type="radio" name="service_type" value="dine_in" {{ $serviceType === 'dine_in' ? 'checked' : '' }}>
                                <span class="service-type-card">
                                    <i class="bi bi-cup-hot"></i>
                                    Dine In
                                </span>
                            </label>
                            <label class="service-type-option">
                                <input type="radio" name="service_type" value="take_away" {{ $serviceType === 'take_away' ? 'checked' : '' }}>
                                <span class="service-type-card">
                                    <i class="bi bi-bag"></i>
                                    Take Away
                                </span>
                            </label>
                        </div>
                    </form>

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
const customerCartStorageKey = 'essensia_customer_cart';
const currentServerCart = @json(array_values($cart ?? []));

function syncCustomerCartBackup(items) {
    if (!Array.isArray(items) || items.length === 0) {
        localStorage.removeItem(customerCartStorageKey);
        return;
    }

    localStorage.setItem(customerCartStorageKey, JSON.stringify(items));
}

if (currentServerCart.length > 0) {
    syncCustomerCartBackup(currentServerCart);
}

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

document.querySelectorAll('.js-cart-options-form').forEach(form => {
    const status = form.querySelector('.cart-options-status');
    const temperature = form.querySelector('.js-temperature-select');
    const ice = form.querySelector('.js-ice-select');
    let timer;

    function syncIceState() {
        if (!temperature || !ice) {
            return;
        }

        const temperatureDisabled = temperature.disabled;
        const isHot = temperature.value === 'hot';
        const iceField = ice.closest('.js-ice-field');

        ice.disabled = temperatureDisabled || isHot;
        ice.setAttribute('aria-disabled', ice.disabled ? 'true' : 'false');

        if (isHot) {
            ice.value = 'normal';
        }

        if (iceField) {
            iceField.classList.toggle('is-disabled', ice.disabled);
        }
    }

    async function saveOptions(shouldReload = false) {
        syncIceState();
        status.textContent = 'Menyimpan...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Pilihan gagal disimpan.');
            }
            status.textContent = 'Tersimpan';
            if (shouldReload) {
                window.location.reload();
            }
        } catch (error) {
            status.textContent = error.message || 'Gagal disimpan';
        }
    }

    form.querySelectorAll('select').forEach(input => {
        input.addEventListener('change', () => saveOptions(['add_on_menu_id', 'variant_id'].includes(input.name)));
    });

    form.querySelectorAll('input[name="note"]').forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(saveOptions, 500);
        });
    });

    syncIceState();
});

document.querySelectorAll('.js-service-type-form').forEach(form => {
    form.querySelectorAll('input[name="service_type"]').forEach(input => {
        input.addEventListener('change', async () => {
            try {
                await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });
            } catch (error) {
                form.submit();
            }
        });
    });
});

document.querySelectorAll('form[action="{{ route('cart.clear') }}"]').forEach(form => {
    form.addEventListener('submit', () => {
        localStorage.removeItem(customerCartStorageKey);
    });
});

document.querySelectorAll('.js-cart-action').forEach(link => {
    link.addEventListener('click', event => {
        const confirmMessage = link.dataset.confirmMessage;
        if (confirmMessage && !window.confirm(confirmMessage)) {
            event.preventDefault();
            return;
        }

        const itemKey = link.dataset.cartKey;
        const action = link.dataset.cartAction;
        let backup = [];

        try {
            backup = JSON.parse(localStorage.getItem(customerCartStorageKey) || '[]');
        } catch (error) {
            backup = [];
        }

        backup = backup.map(item => ({ ...item }));
        const index = backup.findIndex(item => String(item.cart_key || item.id) === String(itemKey));

        if (index === -1) return;

        if (action === 'increase') {
            backup[index].qty = Number(backup[index].qty || 1) + 1;
        } else if (action === 'decrease') {
            backup[index].qty = Number(backup[index].qty || 1) - 1;
            if (backup[index].qty <= 0) {
                backup.splice(index, 1);
            }
        } else if (action === 'remove') {
            backup.splice(index, 1);
        }

        syncCustomerCartBackup(backup);
    });
});
</script>
@endpush
