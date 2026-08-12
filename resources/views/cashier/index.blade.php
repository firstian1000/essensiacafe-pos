@extends('layouts.admin')

@section('title','Kasir')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/cashier.css') }}?v=24">
@endpush

@section('content')
<div class="cashier-page">
    <div class="page-header">
        <div>
            <h1>Kasir</h1>
            <p style="color: #64748B; font-size: 14px; margin: 4px 0 8px 0; font-weight: 500;">Pemesanan langsung di kasir untuk take away</p>
            @if(auth()->user()?->role === 'admin')
                <div class="breadcrumb-custom">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                    <span>Kasir</span>
                </div>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('cashier.store') }}" method="POST" id="cashierForm" class="cashier-layout">
        @csrf

        <section class="cashier-menu-panel">
            <div class="cashier-toolbar">
                <div style="display: flex; gap: 10px; align-items: center; max-width: 480px; width: 100%;">
                    <div class="search-box" style="border: 2px solid #2563EB; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08); background-color: #F8FAFC !important; flex: 1; margin: 0; overflow: hidden;">
                        <i class="bi bi-search" style="color: #2563EB; font-weight: bold;"></i>
                        <input type="search" id="cashierSearch" placeholder="Cari menu..." style="background-color: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; width: 100%; height: 100%; font-size: 15px; background: transparent !important;">
                    </div>
                    <button type="button" id="btnCashierSearch" class="btn btn-primary" style="height: 46px; border-radius: 12px; background-color: #2563EB; border: none; font-weight: 600; padding: 0 20px;">
                        Cari
                    </button>
                </div>

                <div class="cashier-category-filter" id="cashierCategoryFilter">
                    <button type="button" class="active" data-category-id="all">Semua</button>
                    @foreach($categories as $category)
                        <button type="button" data-category-id="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="cashier-menu-grid">
                @foreach($menus as $menu)
                    <button
                        type="button"
                        class="cashier-menu-card"
                        data-menu-id="{{ $menu->id }}"
                        data-name="{{ strtolower($menu->name) }}"
                        data-price="{{ (int) $menu->price }}"
                        data-category-id="{{ $menu->category_id ?? 0 }}">
                        <img src="{{ $menu->image ? asset('storage/'.$menu->image) : asset('images/no-image.png') }}" alt="{{ $menu->name }}">
                        <span class="cashier-menu-name">{{ $menu->name }}</span>
                        <span class="cashier-menu-meta">{{ optional($menu->category)->name ?? 'Tanpa Kategori' }}</span>
                        <strong>Rp {{ number_format($menu->price,0,',','.') }}</strong>
                    </button>
                    <template id="menu-variants-{{ $menu->id }}">
                        @foreach($menu->variants as $variant)
                            <option value="{{ $variant->id }}" data-price="{{ (int) $variant->price }}">
                                {{ $variant->name }} - Rp {{ number_format($variant->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </template>
                @endforeach
            </div>

            <div class="cashier-pagination-wrap">
                <nav class="cashier-pagination" id="cashierPagination" aria-label="Pagination menu kasir"></nav>
                <div class="cashier-pagination-info" id="cashierPaginationInfo"></div>
            </div>
        </section>

        <aside class="cashier-cart-panel">
            <div class="cashier-cart-head">
                <div>
                    <h2>Nota Kasir</h2>
                    <p>Pilih menu, simpan, lalu cetak nota</p>
                </div>
                <i class="bi bi-printer"></i>
            </div>

            <div class="cashier-fields">
                <label>
                    <span>Customer <small style="color:#DC2626;font-weight:900;">*</small></span>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nama pelanggan" required>
                </label>
                <label>
                    <span>Metode</span>
                    <select name="payment_method" id="paymentMethod" required>
                        <option value="cash">Tunai</option>
                        <option value="midtrans">Non Tunai</option>
                    </select>
                </label>
            </div>

            <div class="cashier-service-type">
                <span class="cashier-service-title">
                    <i class="bi bi-bag-check"></i>
                    Layanan
                </span>
                <div class="cashier-service-options">
                    <label>
                        <input type="radio" name="service_type" value="dine_in" {{ old('service_type', 'take_away') === 'dine_in' ? 'checked' : '' }}>
                        <span><i class="bi bi-cup-hot"></i>Dine In</span>
                    </label>
                    <label>
                        <input type="radio" name="service_type" value="take_away" {{ old('service_type', 'take_away') === 'take_away' ? 'checked' : '' }}>
                        <span><i class="bi bi-bag"></i>Take Away</span>
                    </label>
                </div>
            </div>

            <div class="cashier-cart-items" id="cashierCartItems">
                <div class="cart-empty-state">Belum ada menu dipilih.</div>
            </div>

            <input type="hidden" name="paid_amount" id="paidAmountHidden" value="0">
            <input type="hidden" name="submit_action" id="submitAction" value="print_receipt">

            <div class="cashier-summary">
                <div><span>Total Item</span><strong id="totalItems">0</strong></div>
                <div><span>Total</span><strong id="grandTotal">Rp 0</strong></div>
                <label class="paid-field" id="paidField">
                    <span>Uang Diterima</span>
                    <div class="money-input">
                        <span>Rp</span>
                        <input type="number" id="paidAmount" min="0" value="0">
                    </div>
                </label>
                <div class="change-row" id="changeRow"><span>Kembalian</span><strong id="changeTotal">Rp 0</strong></div>
            </div>


            <div class="cashier-action-buttons">
                <button type="submit" class="btn-cashier-submit btn-midtrans-pay" id="btnMidtransPay" data-submit-action="pay_midtrans">
                    <i class="bi bi-credit-card"></i>
                    Bayar Nanti
                </button>
                <button type="submit" class="btn-cashier-submit" data-submit-action="print_receipt">
                <i class="bi bi-receipt-cutoff"></i>
                Cetak Nota
                </button>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
const cart = new Map();
const perPage = 10;
let activeCategory = 'all';
let currentPage = 1;
let filteredCards = [];

const money = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
const cartEl = document.getElementById('cashierCartItems');
const grandEl = document.getElementById('grandTotal');
const totalItemsEl = document.getElementById('totalItems');
const paidEl = document.getElementById('paidAmount');
const paidHiddenEl = document.getElementById('paidAmountHidden');
const changeEl = document.getElementById('changeTotal');
const changeRowEl = document.getElementById('changeRow');
const paidFieldEl = document.getElementById('paidField');
const paymentMethodEl = document.getElementById('paymentMethod');
const searchEl = document.getElementById('cashierSearch');
const paginationEl = document.getElementById('cashierPagination');
const paginationInfoEl = document.getElementById('cashierPaginationInfo');
const menuCards = Array.from(document.querySelectorAll('.cashier-menu-card'));
const submitActionEl = document.getElementById('submitAction');
const midtransPayBtn = document.getElementById('btnMidtransPay');

menuCards.forEach(card => {
    card.addEventListener('click', () => {
        const id = card.dataset.menuId;
        const variantTemplate = document.getElementById(`menu-variants-${id}`);
        const variants = variantTemplate
            ? Array.from(variantTemplate.content.querySelectorAll('option')).map(option => ({
                id: option.value,
                name: option.textContent.split(' - ')[0].trim(),
                price: Number(option.dataset.price || card.dataset.price),
                label: option.textContent.trim(),
            }))
            : [];
        const selectedVariant = variants[0] || null;
        const cartKey = selectedVariant ? `${id}-${selectedVariant.id}` : id;
        const item = cart.get(cartKey) || {
            id,
            key: cartKey,
            name: card.querySelector('.cashier-menu-name').textContent.trim(),
            basePrice: Number(card.dataset.price),
            price: selectedVariant ? Number(selectedVariant.price) : Number(card.dataset.price),
            variantId: selectedVariant ? String(selectedVariant.id) : '',
            variantName: selectedVariant ? selectedVariant.name : '',
            variants,
            qty: 0,
        };
        item.qty += 1;
        cart.set(cartKey, item);
        renderCart();
    });
});

searchEl.addEventListener('input', () => {
    currentPage = 1;
    filterMenus();
});

document.getElementById('btnCashierSearch').addEventListener('click', () => {
    currentPage = 1;
    filterMenus();
});

document.querySelectorAll('#cashierCategoryFilter button').forEach(button => {
    button.addEventListener('click', () => {
        activeCategory = button.dataset.categoryId;
        currentPage = 1;
        document.querySelectorAll('#cashierCategoryFilter button').forEach(item => item.classList.remove('active'));
        button.classList.add('active');
        filterMenus();
    });
});

paidEl.addEventListener('input', renderTotals);
paymentMethodEl.addEventListener('change', renderTotals);

document.querySelectorAll('[data-submit-action]').forEach(button => {
    button.addEventListener('click', () => {
        submitActionEl.value = button.dataset.submitAction;
    });
});

function filterMenus() {
    const keyword = searchEl.value.toLowerCase();
    filteredCards = menuCards.filter(card => {
        const matchName = card.dataset.name.includes(keyword);
        const matchCategory = activeCategory === 'all' || card.dataset.categoryId === activeCategory;
        return matchName && matchCategory;
    });
    renderMenuPage();
}

function renderMenuPage() {
    const total = filteredCards.length;
    const totalPages = Math.max(Math.ceil(total / perPage), 1);
    currentPage = Math.min(Math.max(currentPage, 1), totalPages);
    const start = (currentPage - 1) * perPage;
    const end = Math.min(start + perPage, total);

    menuCards.forEach(card => card.style.display = 'none');
    filteredCards.slice(start, end).forEach(card => card.style.display = '');

    renderPagination(totalPages, total, start, end);
}

function renderPagination(totalPages, total, start, end) {
    const buttons = [];
    buttons.push(pageButton('prev', currentPage - 1, currentPage === 1));

    const pages = compactPages(totalPages, currentPage);
    pages.forEach(page => {
        if (page === '...') {
            buttons.push('<span class="page-ellipsis">...</span>');
        } else {
            buttons.push(pageButton(page, page, false, page === currentPage));
        }
    });

    buttons.push(pageButton('next', currentPage + 1, currentPage === totalPages));
    paginationEl.innerHTML = buttons.join('');
    paginationInfoEl.textContent = total === 0
        ? 'Showing 0 results'
        : `Showing ${start + 1} to ${end} of ${total} results`;

    paginationEl.querySelectorAll('button[data-page]').forEach(button => {
        button.addEventListener('click', () => {
            currentPage = Number(button.dataset.page);
            renderMenuPage();
        });
    });
}

function compactPages(totalPages, page) {
    if (totalPages <= 10) return Array.from({length: totalPages}, (_, i) => i + 1);
    const set = new Set([1, 2, 3, page - 1, page, page + 1, totalPages - 1, totalPages]);
    const pages = [...set].filter(item => item >= 1 && item <= totalPages).sort((a, b) => a - b);
    const output = [];
    pages.forEach((item, index) => {
        if (index > 0 && item - pages[index - 1] > 1) output.push('...');
        output.push(item);
    });
    return output;
}

function pageButton(label, page, disabled = false, active = false) {
    const isPrev = label === 'prev';
    const isNext = label === 'next';
    const content = isPrev ? '<i class="bi bi-chevron-left"></i>' : (isNext ? '<i class="bi bi-chevron-right"></i>' : label);
    const aria = isPrev ? 'Halaman sebelumnya' : (isNext ? 'Halaman berikutnya' : `Halaman ${label}`);
    return `<button type="button" data-page="${page}" class="${active ? 'active' : ''}" aria-label="${aria}" ${disabled ? 'disabled' : ''}>${content}</button>`;
}

function renderCart() {
    if (cart.size === 0) {
        cartEl.innerHTML = '<div class="cart-empty-state">Belum ada menu dipilih.</div>';
        renderTotals();
        return;
    }

    cartEl.innerHTML = Array.from(cart.values()).map((item, index) => {
        const optionsHtml = item.variants.map(variant => {
            const selected = String(item.variantId) === String(variant.id) ? 'selected' : '';

            return '<option value="' + variant.id + '" ' + selected + '>' +
                variant.label +
                '</option>';
        }).join('');

        const variantOptions = item.variants.length > 0
            ? `<label class="cashier-variant-select">
                    <span>Varian</span>
                    <select data-action="variant" data-id="${item.key}" name="items[${index}][variant_id]">
                        ${optionsHtml}
                    </select>
                </label>`
            : `<input type="hidden" name="items[${index}][variant_id]" value="">`;

        return `
        <div class="cashier-cart-item">
            <input type="hidden" name="items[${index}][menu_id]" value="${item.id}">
            <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            <div>
                <strong>${item.name}</strong>
                ${variantOptions}
                <span>${money(item.price)} x ${item.qty}</span>
            </div>
            <div class="qty-actions">
                <button type="button" data-action="minus" data-id="${item.key}"><i class="bi bi-dash"></i></button>
                <span>${item.qty}</span>
                <button type="button" data-action="plus" data-id="${item.key}"><i class="bi bi-plus"></i></button>
            </div>
        </div>
        `;
    }).join('');

    cartEl.querySelectorAll('button[data-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = cart.get(btn.dataset.id);
            if (!item) return;
            item.qty += btn.dataset.action === 'plus' ? 1 : -1;
            if (item.qty <= 0) cart.delete(btn.dataset.id);
            renderCart();
        });
    });

    cartEl.querySelectorAll('select[data-action="variant"]').forEach(select => {
        select.addEventListener('change', () => {
            const item = cart.get(select.dataset.id);
            if (!item) return;

            const variant = item.variants.find(row => String(row.id) === String(select.value));
            if (!variant) return;

            cart.delete(item.key);
            item.variantId = String(variant.id);
            item.variantName = variant.name;
            item.price = Number(variant.price);
            item.key = `${item.id}-${item.variantId}`;

            if (cart.has(item.key)) {
                const existing = cart.get(item.key);
                existing.qty += item.qty;
            } else {
                cart.set(item.key, item);
            }

            renderCart();
        });
    });

    renderTotals();
}

function renderTotals() {
    const items = Array.from(cart.values());
    const total = items.reduce((sum, item) => sum + item.price * item.qty, 0);
    const qty = items.reduce((sum, item) => sum + item.qty, 0);
    const isCash = paymentMethodEl.value === 'cash';
    const paid = isCash ? Number(paidEl.value || 0) : total;

    paidHiddenEl.value = paid;
    paidFieldEl.style.display = isCash ? '' : 'none';
    changeRowEl.style.display = isCash ? '' : 'none';
    midtransPayBtn.classList.toggle('is-hidden', isCash);
    totalItemsEl.textContent = qty;
    grandEl.textContent = money(total);
    changeEl.textContent = money(Math.max(paid - total, 0));
}

document.getElementById('cashierForm').addEventListener('submit', e => {
    if (cart.size === 0) {
        e.preventDefault();
        alert('Pilih minimal satu menu.');
        return;
    }

    renderTotals();

    if (typeof cafeConfig !== 'undefined') {
        const now = new Date();
        const openParts = cafeConfig.openTime.split(':').map(Number);
        const closeParts = cafeConfig.closeTime.split(':').map(Number);
        
        const openTime = new Date();
        openTime.setHours(openParts[0], openParts[1], 0, 0);
        
        const closeTime = new Date();
        closeTime.setHours(closeParts[0], closeParts[1], 0, 0);
        
        const orderLimitTime = new Date(closeTime.getTime() - (cafeConfig.orderLimitMinutes * 60 * 1000));

        if (now < openTime || now >= orderLimitTime) {
            e.preventDefault();
            alert('Pemesanan sudah ditutup untuk hari ini karena telah melewati batas akhir pemesanan.');
            return;
        }
    }
});

filterMenus();
renderTotals();
</script>
@endpush
