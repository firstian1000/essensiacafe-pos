<div class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">

        <img src="{{ asset('images/logo.png') }}?v=5" alt="Essensia Koffie">

        <div class="logo-text">
            <h3>Essensia Koffie</h3>
            <small>Coffee & Space</small>
        </div>

    </div>

    <!-- Menu -->
    <ul class="sidebar-menu">

        <li>
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('categories.index') }}"
               class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                <span>Kategori</span>
            </a>
        </li>

        <li>
            <a href="{{ route('menus.index') }}"
               class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                <i class="bi bi-cup-hot"></i>
                <span>Menu</span>
            </a>
        </li>

        <li>
            <a href="{{ route('tables.index') }}"
               class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>Meja</span>
            </a>
        </li>

        <li>
            <a href="{{ route('cashier.index') }}"
               class="{{ request()->routeIs('cashier.*') ? 'active' : '' }}">
                <i class="bi bi-calculator"></i>
                <span>Kasir</span>
            </a>
        </li>

        <li>
            <a href="{{ route('orders.index') }}"
               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Pesanan</span>
            </a>
        </li>

        <li>
            <a href="{{ route('payments.index') }}"
               class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Pembayaran</span>
            </a>
        </li>

    </ul>


</div>