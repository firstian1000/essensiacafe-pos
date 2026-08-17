@extends('layouts.customer')

@section('content')

<div class="menu-page">

    {{-- ===== HERO HEADER ===== --}}
    <div class="menu-hero">
        <div class="menu-hero-left">
            <p class="hero-greeting">Selamat datang! 👋</p>
            <h1 class="hero-heading">Pilih menu favoritmu</h1>
            <p class="hero-sub">Semua menu dibuat dengan bahan pilihan<br>untuk pengalaman terbaik.</p>
        </div>
        <div class="menu-hero-right">
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    placeholder="Cari makanan atau minuman...">
            </div>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
    <div class="alert-custom alert-success-custom fade-up">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
    </div>
    @endif

    @php
        if (!function_exists('getCategoryGroupKey')) {
            function getCategoryGroupKey($catName) {
                $name = strtolower($catName);
                if (str_contains($name, 'coffee') || str_contains($name, 'latte') || str_contains($name, 'brew') || str_contains($name, 'signature') || str_contains($name, 'americano') || str_contains($name, 'sanger')) {
                    return 'coffee';
                }
                if (str_contains($name, 'tea') || str_contains($name, 'non coffee') || str_contains($name, 'milkbase') || str_contains($name, 'refreshment')) {
                    return 'noncoffee';
                }
                if (str_contains($name, 'dimsum') || str_contains($name, 'mentai') || str_contains($name, 'original') || str_contains($name, 'tartar') || str_contains($name, 'moza')) {
                    return 'dimsum';
                }
                if (str_contains($name, 'snack') || str_contains($name, 'main course') || str_contains($name, 'chiki') || str_contains($name, 'showcase') || str_contains($name, 'makanan') || str_contains($name, 'indomie') || str_contains($name, 'pisang') || str_contains($name, 'roti')) {
                    return 'food';
                }
                if (str_contains($name, 'terea') || str_contains($name, 'veev')) {
                    return 'vape';
                }
                return 'other';
            }
        }
    @endphp

    {{-- ===== CATEGORY FILTER ===== --}}
    <div class="filter-bar">
        <button class="filter-pill active" data-category-id="all">
            <i class="bi bi-grid-2x2-fill"></i>
            Semua
        </button>
        @foreach($categories as $category)
        <button class="filter-pill" data-category-id="{{ $category->id }}">
            <i class="bi bi-tag-fill"></i>
            {{ $category->name }}
        </button>
        @endforeach
    </div>

    {{-- ===== MENU GRID ===== --}}
    <div class="menu-grid" id="menu-list">

        @foreach($menus as $menu)

        @php
            $categoryName = optional($menu->category)->name ?? 'Lainnya';
            $groupKey = getCategoryGroupKey($categoryName);
            $categoryColors = [
                'coffee'    => 'badge-coffee',
                'noncoffee' => 'badge-noncoffee',
                'dimsum'    => 'badge-milkbase',
                'food'      => 'badge-maincourse',
                'vape'      => 'badge-snack',
            ];
            $badgeClass = $categoryColors[$groupKey] ?? 'badge-default';
            $inCart = collect(session('cart', []))->contains('id', $menu->id);
        @endphp

        <div class="menu-card-wrap menu-item {{ !$menu->status ? 'out-of-stock' : '' }}"
             data-category="{{ strtolower($categoryName) }}"
             data-category-id="{{ $menu->category_id ?? 0 }}"
             data-group="{{ $groupKey }}">

            <div class="menu-card">

                {{-- Image --}}
                <div class="menu-card-img-wrap">

                    @if($menu->is_recommended)
                    <div class="recommend-pin">
                        <i class="bi bi-stars"></i>
                        Recommend
                    </div>
                    @endif

                    @if($menu->image)
                    <img src="{{ asset('storage/'.$menu->image) }}"
                         alt="{{ $menu->name }}"
                         class="menu-card-img {{ !$menu->status ? 'img-grayscale' : '' }}">
                    @else
                    <img src="{{ asset('images/no-image.png') }}"
                         alt="{{ $menu->name }}"
                         class="menu-card-img">
                    @endif

                    {{-- Wish / Heart --}}
                    <button class="wish-btn" aria-label="Favorit">
                        <i class="bi bi-heart"></i>
                    </button>

                    {{-- Stock habis overlay --}}
                    @if(!$menu->status)
                    <div class="stock-overlay">
                        <span>Stok Habis</span>
                    </div>
                    @endif

                </div>

                {{-- Body --}}
                <div class="menu-card-body">

                    {{-- Badges --}}
                    <div class="menu-badges">
                        <span class="cat-badge {{ $badgeClass }}">{{ $categoryName }}</span>
                        {{-- Best Seller badge jika harga tertinggi di kategori (opsional) --}}
                    </div>

                    {{-- Name --}}
                    <h3 class="menu-card-name">{{ $menu->name }}</h3>

                    {{-- Description --}}
                    <p class="menu-card-desc">{{ $menu->description }}</p>

                    {{-- Price --}}
                    <div class="menu-card-price">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </div>

                    {{-- Add to Cart --}}
                    @if($menu->status)
                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-cart-form">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        @if($menu->variants->isNotEmpty())
                        <label class="menu-variant-select">
                            <span>Varian</span>
                            <select name="variant_id">
                                @foreach($menu->variants as $variant)
                                    <option value="{{ $variant->id }}">
                                        {{ $variant->name }} - Rp {{ number_format($variant->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        @endif
                        <button type="submit" class="btn-add-cart {{ $inCart ? 'btn-add-cart--active' : '' }}">
                            <i class="bi bi-plus-lg"></i>
                            Tambah
                        </button>
                    </form>
                    @else
                    <button class="btn-add-cart btn-add-cart--disabled" disabled>
                        <i class="bi bi-x-circle"></i>
                        Stok Habis
                    </button>
                    @endif

                </div>

            </div>

        </div>

        @endforeach

    </div>

    {{-- ===== EMPTY STATE ===== --}}
    <div class="empty-menu" id="empty-menu" style="display:none;">
        <i class="bi bi-search fs-1 text-muted mb-3"></i>
        <h4>Menu tidak ditemukan</h4>
        <p class="text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
    </div>

</div>

{{-- ===== FLOATING BOTTOM BAR ===== --}}
@php
    $floatCart = session('cart', []);
    $floatCount = collect($floatCart)->sum('qty');
    $floatTotal = collect($floatCart)->sum(fn($i) => ((int) $i['price'] + (int) ($i['add_on_price'] ?? 0)) * $i['qty']);
    $floatItems = array_slice(array_values($floatCart), 0, 3);
@endphp

@if($floatCount > 0)
<div class="float-bar show" id="floatBar">
    <div class="float-bar-left">
        <div class="float-bar-cart-icon">
            <i class="bi bi-cart3"></i>
            <span class="float-bar-badge">{{ $floatCount }}</span>
        </div>
        <div class="float-bar-info">
            <span class="float-bar-label">{{ $floatCount }} Item di Keranjang</span>
            <span class="float-bar-sub">Lihat keranjang untuk checkout</span>
        </div>
    </div>
    <div class="float-bar-mid float-bar-names">
        @foreach($floatItems as $fi)
        <div class="float-bar-name-item">
            <span>{{ $fi['name'] }}</span>
            <strong>{{ $fi['qty'] }}</strong>
        </div>
        @endforeach
    </div>
    <div class="float-bar-right">
        <div class="float-bar-total-label">Total</div>
        <div class="float-bar-total">Rp {{ number_format($floatTotal,0,',','.') }}</div>
    </div>
    <a href="{{ route('cart.index') }}" class="float-bar-btn">
        <span class="float-bar-btn-text">Lihat Keranjang</span>
        <span class="float-bar-btn-mobile-text">Lanjutkan</span>
        <i class="bi bi-arrow-right"></i>
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
// ===== SEARCH =====
const searchInput = document.getElementById('search');
const menuItems   = document.querySelectorAll('.menu-item');
const emptyEl     = document.getElementById('empty-menu');

function filterMenus() {
    const keyword  = searchInput.value.toLowerCase().trim();
    const activeBtn = document.querySelector('.filter-pill.active');
    const activeCatId = activeBtn ? activeBtn.dataset.categoryId : 'all';

    let visible = 0;
    menuItems.forEach(item => {
        const matchSearch = item.innerText.toLowerCase().includes(keyword);
        const matchCategory = activeCatId === 'all' || item.dataset.categoryId === activeCatId;
        const show = matchSearch && matchCategory;
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    emptyEl.style.display = visible === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', filterMenus);

// ===== CATEGORY FILTER =====
document.querySelectorAll('.filter-pill').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filterMenus();
    });
});

// ===== WISH BUTTON =====
document.querySelectorAll('.wish-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-heart');
        icon.classList.toggle('bi-heart-fill');
        this.classList.toggle('wished');
    });
});

// ===== ADD TO CART WITHOUT FULL RELOAD =====
const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
const cartUrl = @json(route('cart.index'));
const cartStorageKey = 'essensia_customer_cart';

document.querySelectorAll('.js-add-cart-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const button = form.querySelector('.btn-add-cart');
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-check-lg"></i> Ditambahkan';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Menu gagal ditambahkan.');
            }

            button.classList.add('btn-add-cart--active');
            if (data.cart_items) {
                localStorage.setItem(cartStorageKey, JSON.stringify(data.cart_items));
            }
            updateCartNavbar(data);
            updateFloatCart(data);
        } catch (error) {
            alert(error.message || 'Menu gagal ditambahkan ke keranjang.');
            button.innerHTML = originalHtml;
        } finally {
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }, 650);
        }
    });
});

function updateCartNavbar(data) {
    const navCart = document.querySelector('.cart-btn-nav');
    if (!navCart) return;

    navCart.classList.toggle('has-items', data.cart_count > 0);

    let totalEl = navCart.querySelector('.cart-total-nav');
    const textWrap = navCart.querySelector('.cart-btn-text');
    if (!totalEl && textWrap) {
        totalEl = document.createElement('span');
        totalEl.className = 'cart-total-nav';
        textWrap.appendChild(totalEl);
    }
    if (totalEl) totalEl.textContent = rupiah(data.cart_total);

    let badge = navCart.querySelector('.cart-badge-nav');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'cart-badge-nav';
        navCart.appendChild(badge);
    }
    badge.textContent = data.cart_count;
}

function updateFloatCart(data) {
    let floatBar = document.getElementById('floatBar');
    if (!floatBar) {
        floatBar = document.createElement('div');
        floatBar.id = 'floatBar';
        floatBar.className = 'float-bar show';
        document.body.appendChild(floatBar);
    }

    const items = (data.preview_items || []).map(item => `
        <div class="float-bar-name-item">
            <span>${item.name}</span>
            <strong>${item.qty}</strong>
        </div>
    `).join('');

    floatBar.innerHTML = `
        <div class="float-bar-left">
            <div class="float-bar-cart-icon">
                <i class="bi bi-cart3"></i>
                <span class="float-bar-badge">${data.cart_count}</span>
            </div>
            <div class="float-bar-info">
                <span class="float-bar-label">${data.cart_count} Item di Keranjang</span>
                <span class="float-bar-sub">Lihat keranjang untuk checkout</span>
            </div>
        </div>
        <div class="float-bar-mid float-bar-names">${items}</div>
        <div class="float-bar-right">
            <div class="float-bar-total-label">Total</div>
            <div class="float-bar-total">${rupiah(data.cart_total)}</div>
        </div>
        <a href="${cartUrl}" class="float-bar-btn">
            <span class="float-bar-btn-text">Lihat Keranjang</span>
            <span class="float-bar-btn-mobile-text">Lanjutkan</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    `;
}
</script>
@endpush
