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
            $inCart = isset(session('cart', [])[$menu->id]);
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
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <button class="btn-add-cart {{ $inCart ? 'btn-add-cart--active' : '' }}">
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
    $floatTotal = collect($floatCart)->sum(fn($i) => $i['price'] * $i['qty']);
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
    <div class="float-bar-mid">
        @foreach($floatItems as $fi)
        <div class="float-bar-thumb-wrap">
            @if(!empty($fi['image']))
            <img src="{{ asset('storage/'.$fi['image']) }}" class="float-bar-thumb" alt="{{ $fi['name'] }}">
            @endif
            <span class="float-bar-thumb-count">{{ $fi['qty'] }}</span>
        </div>
        @endforeach
    </div>
    <div class="float-bar-right">
        <div class="float-bar-total-label">Total</div>
        <div class="float-bar-total">Rp {{ number_format($floatTotal,0,',','.') }}</div>
    </div>
    <a href="{{ route('cart.index') }}" class="float-bar-btn">
        Lihat Keranjang <i class="bi bi-arrow-right"></i>
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
</script>
@endpush


