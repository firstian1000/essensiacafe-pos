<div class="sidebar" id="sidebar">
    @php
        $isCashierArea = request()->is('cashier', 'cashier/*', 'kasir/*')
            || request('area') === 'cashier'
            || auth()->user()?->role === 'cashier';
        $currentUser = $isCashierArea && auth('cashier')->check()
            ? auth('cashier')->user()
            : (auth('admin')->user() ?? auth('cashier')->user());
        $activeArea = $isCashierArea || $currentUser?->role === 'cashier' ? 'cashier' : 'admin';
    @endphp

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

        @if($currentUser?->role === 'admin')
        <li>
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('categories.index', ['area' => $activeArea]) }}"
               class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                <span>Kategori</span>
            </a>
        </li>

        <li>
            <a href="{{ route('menus.index', ['area' => $activeArea]) }}"
               class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                <i class="bi bi-cup-hot"></i>
                <span>Menu</span>
            </a>
        </li>

        <li>
            <a href="{{ route('stocks.index') }}"
               class="{{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Stok</span>
            </a>
        </li>

        <li>
            <a href="{{ route('expenses.index') }}"
               class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Pengeluaran</span>
            </a>
        </li>

        <li>
            <a href="{{ route('tables.index', ['area' => $activeArea]) }}"
               class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>Meja</span>
            </a>
        </li>
        @endif

        @if($currentUser?->role === 'cashier')
            <li>
                <a href="{{ route('categories.index', ['area' => $activeArea]) }}"
                   class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i>
                    <span>Kategori</span>
                </a>
            </li>

            <li>
                <a href="{{ route('menus.index', ['area' => $activeArea]) }}"
                   class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                    <i class="bi bi-cup-hot"></i>
                    <span>Menu</span>
                </a>
            </li>

            <li>
                <a href="{{ route('tables.index', ['area' => $activeArea]) }}"
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
        @endif

        <li>
            <a href="{{ route('orders.index', ['area' => $activeArea]) }}"
               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Pesanan</span>
            </a>
        </li>

        @if($currentUser?->role === 'admin')
            <li>
                <a href="{{ route('settings.index') }}"
                   class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
        @endif

    </ul>


</div>
