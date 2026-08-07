<nav class="topbar">

    <div class="topbar-left">

        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="page-title">
            <h3>Cafe Management</h3>
        </div>

    </div>

    <div class="topbar-right">

        @php
            $pendingNotifs = \App\Models\Order::where('status', 'pending')
                                ->orWhere('payment_status', 'pending')
                                ->latest()
                                ->take(5)
                                ->get();
        @endphp

        <div class="dropdown">
            <button class="notification" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative;">
                <i class="bi bi-bell"></i>
                @if($pendingNotifs->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 300px; border: none; border-radius: 12px; padding: 0;">
                <li class="p-3 border-bottom">
                    <h6 class="m-0 fw-bold">Notifikasi</h6>
                </li>
                @forelse($pendingNotifs as $notif)
                <li>
                    <a class="dropdown-item py-2 border-bottom" href="{{ route('orders.show', $notif->id) }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ $notif->created_at->diffForHumans() }}</small>
                                <span class="d-block text-truncate" style="max-width: 200px;">
                                    Pesanan #{{ $notif->id }} menunggu diproses/dibayar
                                </span>
                            </div>
                        </div>
                    </a>
                </li>
                @empty
                <li><span class="dropdown-item py-3 text-center text-muted">Belum ada notifikasi baru</span></li>
                @endforelse
                <li>
                    <a class="dropdown-item text-center py-2 text-primary fw-bold" style="border-radius: 0 0 12px 12px;" href="{{ route('orders.index') }}">
                        Lihat Semua Pesanan
                    </a>
                </li>
            </ul>
        </div>

        <div class="admin-profile">

            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div>

                <h6>{{ auth()->user()->name ?? 'Admin' }}</h6>

                <small>Essensia Koffie</small>

            </div>

        </div>
        <a href="{{ route('logout.get') }}" class="topbar-logout-link" title="Keluar">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>

    </div>

</nav>

