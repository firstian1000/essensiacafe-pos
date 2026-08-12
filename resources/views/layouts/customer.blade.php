<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Essensia Koffie - Order</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <meta name="description" content="Pesan menu favoritmu di Essensia Koffie. Fresh Coffee, Good Vibes.">



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/customer/cafe.css') }}?v=29">



    <link rel="stylesheet" href="{{ asset('css/customer/responsive-fix.css') }}?v=13">

    @stack('head')

</head>

<body>



{{-- ===== NAVBAR CUSTOMER ===== --}}

<nav class="customer-navbar" id="customerNavbar">

    <div class="customer-navbar-inner">



        <a href="{{ session('table_token') ? route('customer.menu', session('table_token')) : '#' }}"

           class="navbar-brand-custom">

            <img src="{{ asset('images/logo.png') }}?v=5" alt="Essensia Koffie" class="brand-logo-img" style="max-height: 60px; height: 60px; width: auto; object-fit: contain;">
            <span class="brand-copy">
                <strong>Essensia Koffie</strong>
                <small>Coffee & Space</small>
            </span>

        </a>



        {{-- Right Controls --}}

        <div class="navbar-controls">



            {{-- Meja Info --}}

            @if(session('table_number'))

            <div class="table-pill">

                <i class="bi bi-grid-3x3"></i>

                <span>Nomor Meja : {{ session('table_number') }}</span>

                <i class="bi bi-chevron-down small"></i>

            </div>

            @endif



            {{-- Keranjang --}}

            @php

                $cartNav = session('cart', []);

                $cartCount = collect($cartNav)->sum('qty');

                $cartTotal = collect($cartNav)->sum(fn($i) => ((int) $i['price'] + (int) ($i['add_on_price'] ?? 0)) * $i['qty']);

            @endphp



            <a href="{{ route('cart.index') }}" class="cart-btn-nav {{ $cartCount > 0 ? 'has-items' : '' }}">

                <i class="bi bi-cart3"></i>

                <div class="cart-btn-text">

                    <span class="cart-label">Keranjang</span>

                    @if($cartCount > 0)

                    <span class="cart-total-nav">Rp {{ number_format($cartTotal,0,',','.') }}</span>

                    @endif

                </div>

                @if($cartCount > 0)

                <span class="cart-badge-nav">{{ $cartCount }}</span>

                @endif

            </a>

        </div>



    </div>

</nav>



{{-- ===== MAIN CONTENT ===== --}}

<main class="customer-main">

    @yield('content')

</main>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
(function () {
    const refreshEveryMs = 15000;
    const lastRefreshKey = 'essensia_customer_last_auto_refresh';
    const skippedPathPatterns = [
        /\/cart\/?$/,
        /\/checkout\/?$/,
    ];

    const initialFormState = Array.from(document.querySelectorAll('form')).map(form => new FormData(form).toString()).join('&');

    function isFormDirty() {
        const currentFormState = Array.from(document.querySelectorAll('form')).map(form => new FormData(form).toString()).join('&');
        return currentFormState !== initialFormState;
    }

    function isUserEditing() {
        const active = document.activeElement;
        if (!active) return false;

        return ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName) || active.isContentEditable;
    }

    function shouldSkipAutoRefresh() {
        const path = window.location.pathname;

        return document.hidden
            || skippedPathPatterns.some(pattern => pattern.test(path))
            || isUserEditing()
            || isFormDirty();
    }

    function autoRefreshPage() {
        if (shouldSkipAutoRefresh()) return;

        const now = Date.now();
        const lastRefresh = Number(sessionStorage.getItem(lastRefreshKey) || 0);

        if (now - lastRefresh < refreshEveryMs - 1000) return;

        sessionStorage.setItem(lastRefreshKey, String(now));
        window.location.reload();
    }

    setInterval(autoRefreshPage, refreshEveryMs);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            setTimeout(autoRefreshPage, 800);
        }
    });
})();
</script>



</body>

</html>
