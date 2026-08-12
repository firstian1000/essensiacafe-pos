@php
    $isCashierArea = request()->is('cashier', 'cashier/*', 'kasir/*') || request('area') === 'cashier';
    $navbarUser = $isCashierArea && auth('cashier')->check()
        ? auth('cashier')->user()
        : (auth('admin')->user() ?? auth('cashier')->user());
    $navbarArea = $navbarUser?->role === 'cashier' ? 'cashier' : 'admin';
@endphp

<nav class="topbar">

    <div class="topbar-left">

        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="page-title">
            <h3>{{ $navbarUser?->role === 'cashier' ? 'Kasir Management' : 'Admin Management' }}</h3>
        </div>

    </div>

    <div class="topbar-right">

        <!-- Real-time Clock & Countdown Timer -->
        @if(isset($cafeSettings))
        <div class="d-flex align-items-center gap-3 me-3 text-dark px-3 py-1 bg-light rounded-pill border shadow-sm topbar-timer-container" style="font-size: 0.9rem; font-weight: 600; height: 38px;">
            <div class="d-flex align-items-center gap-1 text-primary">
                <i class="bi bi-clock-fill"></i>
                <span id="topbar-realtime-clock">00:00:00</span>
            </div>
            <div class="vr bg-secondary" style="height: 15px; width: 1px; opacity: 0.3;"></div>
            <div class="d-flex align-items-center gap-1 text-danger">
                <i class="bi bi-hourglass-split"></i>
                <span id="topbar-countdown-timer">Tutup: 00:00:00</span>
            </div>
        </div>

        <!-- Modal Peringatan Operasional -->
        <div class="modal fade" id="opAlertModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 pb-0 justify-content-end" style="padding: 1rem 1rem 0 0;">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="border: none; background: none; font-size: 1.2rem;"></button>
                    </div>
                    <div class="modal-body text-center pt-0 px-4 pb-4">
                        <div class="alert-icon-container mb-3 d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-bell-fill text-warning fs-1" id="opAlertIcon"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark" id="opAlertTitle">Pemberitahuan</h4>
                        <p class="text-secondary mb-4" id="opAlertMessage">Pemberitahuan penting tentang operasional kafe.</p>
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 12px; background: #2563EB; border: none;">Dimengerti</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clockEl = document.getElementById('topbar-realtime-clock');
            const timerEl = document.getElementById('topbar-countdown-timer');

            if (!clockEl || !timerEl) return;

            const cafeConfig = {
                operationalEnabled: "{{ $cafeSettings['operational_settings_enabled'] ?? '1' }}" === '1',
                openTime: "{{ $cafeSettings['cafe_open_time'] ?? '08:00' }}",
                closeTime: "{{ $cafeSettings['cafe_close_time'] ?? '22:00' }}",
                shiftEnabled: "{{ $cafeSettings['shift_settings_enabled'] ?? '1' }}" === '1',
                shiftDurationHours: parseInt("{{ $cafeSettings['shift_duration_hours'] ?? '7' }}"),
                closeOrderEnabled: "{{ $cafeSettings['close_order_settings_enabled'] ?? '1' }}" === '1',
                beforeShiftNotifMinutes: parseInt("{{ $cafeSettings['before_shift_notif_minutes'] ?? '15' }}"),
                beforeCloseNotifMinutes: parseInt("{{ $cafeSettings['before_close_notif_minutes'] ?? '15' }}"),
                orderLimitMinutes: parseInt("{{ $cafeSettings['order_limit_minutes'] ?? '10' }}")
            };

            function playNotifSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                    osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.15);
                    osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.30);
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.5);
                } catch (e) {
                    console.log("Audio context sound blocked or unsupported");
                }
            }

            function showOpAlert(title, message, isDanger = false) {
                const modalEl = document.getElementById('opAlertModal');
                if (!modalEl) return;

                document.getElementById('opAlertTitle').innerText = title;
                document.getElementById('opAlertMessage').innerText = message;
                
                const iconEl = document.getElementById('opAlertIcon');
                if (isDanger) {
                    iconEl.className = 'bi bi-exclamation-triangle-fill text-danger fs-1';
                } else {
                    iconEl.className = 'bi bi-bell-fill text-warning fs-1';
                }

                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
                playNotifSound();
            }

            const modalEl = document.getElementById('opAlertModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
            }

            function parseTimeToday(timeStr) {
                const parts = timeStr.split(':').map(Number);
                const d = new Date();
                d.setHours(parts[0], parts[1], 0, 0);
                return d;
            }

            function updateTimers() {
                const now = new Date();
                
                // Clock update
                const timeString = now.toTimeString().split(' ')[0];
                clockEl.innerText = timeString;

                if (!cafeConfig.operationalEnabled) {
                    timerEl.innerText = 'Operasional Aktif';
                    timerEl.parentElement.classList.remove('text-danger');
                    timerEl.parentElement.classList.add('text-success');
                    timerEl.parentElement.style.color = '#16A34A';
                    return;
                }

                const openTime = parseTimeToday(cafeConfig.openTime);
                const closeTime = parseTimeToday(cafeConfig.closeTime);
                
                const shiftChangeTime = new Date(openTime.getTime() + (cafeConfig.shiftDurationHours * 60 * 60 * 1000));
                
                const shiftWarningTime = new Date(shiftChangeTime.getTime() - (cafeConfig.beforeShiftNotifMinutes * 60 * 1000));
                const closeWarningTime = new Date(closeTime.getTime() - (cafeConfig.beforeCloseNotifMinutes * 60 * 1000));
                const orderLimitTime = new Date(closeTime.getTime() - (cafeConfig.orderLimitMinutes * 60 * 1000));

                const dateKey = now.toDateString();

                // 1. Shift warning (15m before shift change)
                if (cafeConfig.shiftEnabled && now >= shiftWarningTime && now < shiftChangeTime) {
                    const key = `shift_warning_${dateKey}`;
                    if (!localStorage.getItem(key)) {
                        localStorage.setItem(key, 'true');
                        showOpAlert('Peringatan Shift Kerja', `Perhatian! ${cafeConfig.beforeShiftNotifMinutes} menit lagi adalah waktu ganti shift kerja.`);
                    }
                }

                // 2. Shift change time
                if (cafeConfig.shiftEnabled && now >= shiftChangeTime && now < new Date(shiftChangeTime.getTime() + 60000)) {
                    const key = `shift_change_${dateKey}`;
                    if (!localStorage.getItem(key)) {
                        localStorage.setItem(key, 'true');
                        showOpAlert('Waktu Ganti Shift', 'Waktu ganti shift kerja! Harap lakukan serah terima operasional kasir.', true);
                    }
                }

                // 3. Cafe close warning (15m before closing)
                if (cafeConfig.closeOrderEnabled && now >= closeWarningTime && now < closeTime) {
                    const key = `close_warning_${dateKey}`;
                    if (!localStorage.getItem(key)) {
                        localStorage.setItem(key, 'true');
                        showOpAlert('Peringatan Kafe Tutup', `Perhatian! Kafe akan tutup dalam ${cafeConfig.beforeCloseNotifMinutes} menit.`);
                    }
                }

                // 4. Order limit warning (10m before closing)
                if (cafeConfig.closeOrderEnabled && now >= orderLimitTime && now < closeTime) {
                    const key = `order_limit_${dateKey}`;
                    if (!localStorage.getItem(key)) {
                        localStorage.setItem(key, 'true');
                        showOpAlert('Batas Akhir Pemesanan', `Batas akhir pemesanan telah tercapai (${cafeConfig.orderLimitMinutes} menit sebelum tutup). Pemesanan baru ditolak.`, true);
                    }
                }

                // Update countdown display
                if (now < closeTime && now >= openTime) {
                    const diffMs = closeTime - now;
                    const diffHours = Math.floor(diffMs / 3600000);
                    const diffMins = Math.floor((diffMs % 3600000) / 60000);
                    const diffSecs = Math.floor((diffMs % 60000) / 1000);
                    
                    const hoursPad = String(diffHours).padStart(2, '0');
                    const minsPad = String(diffMins).padStart(2, '0');
                    const secsPad = String(diffSecs).padStart(2, '0');
                    
                    timerEl.innerText = `Tutup: ${hoursPad}:${minsPad}:${secsPad}`;
                    timerEl.parentElement.classList.remove('text-success');
                    timerEl.parentElement.classList.add('text-danger');
                    timerEl.parentElement.style.color = '#DC2626'; // Text danger red
                } else {
                    timerEl.innerText = 'Kafe Tutup';
                    timerEl.parentElement.classList.remove('text-success');
                    timerEl.parentElement.classList.add('text-danger');
                    timerEl.parentElement.style.color = '#6B7280'; // Text secondary gray
                }
            }

            setInterval(updateTimers, 1000);
            updateTimers();
        });
        </script>
        @endif

        @php
            $pendingNotifs = \App\Models\Order::where('status', 'pending')
                                ->orWhere('payment_status', 'pending')
                                ->latest()
                                ->take(5)
                                ->get();
        @endphp

        @if($navbarUser?->role === 'cashier')
        <!-- Voice Order Notification Toggle Button -->
        <div class="d-flex align-items-center me-2">
            <button type="button" id="btn-audio-toggle" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1" style="font-size: 0.82rem; transition: all 0.2s ease;">
                <i class="bi bi-volume-up-fill" id="audio-toggle-icon"></i>
                <span id="audio-toggle-text">Suara: ON</span>
            </button>
        </div>
        @endif

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
                    <a class="dropdown-item py-2 border-bottom" href="{{ route('orders.show', ['order' => $notif->id, 'area' => $navbarArea]) }}">
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
                    <a class="dropdown-item text-center py-2 text-primary fw-bold" style="border-radius: 0 0 12px 12px;" href="{{ route('orders.index', ['area' => $navbarArea]) }}">
                        Lihat Semua Pesanan
                    </a>
                </li>
            </ul>
        </div>

        <!-- Floating Realtime Order Toast Popup Container -->
        <div id="order-toast-container" style="position: fixed; top: 75px; right: 25px; z-index: 9999; max-width: 380px; width: 100%; pointer-events: none;"></div>

        @if($navbarUser?->role === 'cashier')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            let latestOrderId = @json(\App\Models\Order::max('id') ?: 0);
            let soundEnabled = localStorage.getItem('order_sound_enabled') !== 'false';
            let isCheckingNewOrders = false;
            const notifiedStorageKey = 'essensia_notified_order_ids';
            const notifiedOrderIds = new Set(JSON.parse(localStorage.getItem(notifiedStorageKey) || '[]'));

            const toggleBtn = document.getElementById('btn-audio-toggle');
            const toggleIcon = document.getElementById('audio-toggle-icon');
            const toggleText = document.getElementById('audio-toggle-text');

            function updateAudioButtonUI() {
                if (!toggleBtn) return;
                if (soundEnabled) {
                    toggleBtn.className = 'btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm';
                    toggleIcon.className = 'bi bi-volume-up-fill';
                    toggleText.innerText = 'Suara: ON';
                } else {
                    toggleBtn.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1';
                    toggleIcon.className = 'bi bi-volume-mute-fill';
                    toggleText.innerText = 'Suara: OFF';
                }
            }

            if (toggleBtn) {
                updateAudioButtonUI();
                toggleBtn.addEventListener('click', function () {
                    soundEnabled = !soundEnabled;
                    localStorage.setItem('order_sound_enabled', soundEnabled);
                    updateAudioButtonUI();

                    if (soundEnabled) {
                        playChimeSound();
                        speakIndonesianVoice("Suara notifikasi pesanan diaktifkan");
                    }
                });
            }

            // Play Bell Chime Sound via Web Audio API
            function playChimeSound() {
                if (!soundEnabled) return;
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc1 = ctx.createOscillator();
                    const osc2 = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc1.type = 'sine';
                    osc2.type = 'triangle';

                    osc1.frequency.setValueAtTime(587.33, ctx.currentTime);
                    osc1.frequency.setValueAtTime(880.00, ctx.currentTime + 0.15);

                    osc2.frequency.setValueAtTime(1174.66, ctx.currentTime);
                    osc2.frequency.setValueAtTime(1760.00, ctx.currentTime + 0.15);

                    gain.gain.setValueAtTime(0.4, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);

                    osc1.connect(gain);
                    osc2.connect(gain);
                    gain.connect(ctx.destination);

                    osc1.start(ctx.currentTime);
                    osc2.start(ctx.currentTime);
                    osc1.stop(ctx.currentTime + 0.8);
                    osc2.stop(ctx.currentTime + 0.8);
                } catch (e) {
                    console.log('Audio Context Error:', e);
                }
            }

            // Speak Voice in Indonesian using SpeechSynthesisUtterance
            function speakIndonesianVoice(text) {
                if (!soundEnabled || !('speechSynthesis' in window)) return;
                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    utterance.pitch = 1.0;
                    window.speechSynthesis.speak(utterance);
                } catch (e) {
                    console.log('Speech Synthesis Error:', e);
                }
            }

            // Show Toast Notification Banner
            function showOrderToast(order) {
                const container = document.getElementById('order-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'card border-0 shadow-lg mb-2 text-dark bg-white overflow-hidden animate__animated animate__fadeInRight';
                toast.style.cssText = 'border-radius: 16px; border-left: 6px solid #2563EB !important; pointer-events: auto; transform: translateY(-5px); transition: all 0.3s ease;';

                toast.innerHTML = `
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                    <i class="bi bi-bell-fill me-1"></i> PESANAN BARU!
                                </span>
                                <small class="text-muted" style="font-size: 0.8rem;">${order.time}</small>
                            </div>
                            <button type="button" class="btn-close" style="font-size: 0.75rem;" onclick="this.closest('.card').remove()"></button>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 1rem;">
                            ${order.table_label} - <span class="text-primary">${order.customer_name}</span>
                        </h6>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <span class="fw-bold text-success" style="font-size: 0.95rem;">${order.total_amount}</span>
                            <a href="${order.url}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="font-size: 0.8rem;">
                                <i class="bi bi-eye-fill me-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                `;

                container.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 12000);
            }

            function rememberNotifiedOrder(orderId) {
                notifiedOrderIds.add(Number(orderId));
                const recentIds = Array.from(notifiedOrderIds).slice(-100);
                localStorage.setItem(notifiedStorageKey, JSON.stringify(recentIds));
            }

            // Poll server every 5 seconds for new orders
            function checkNewOrders() {
                if (isCheckingNewOrders) return;
                isCheckingNewOrders = true;

                fetch("{{ route('orders.checkNew') }}?last_id=" + latestOrderId, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.has_new && data.orders.length > 0) {
                        latestOrderId = data.latest_id;

                        const freshOrders = data.orders.filter(order => {
                            const orderId = Number(order.id);
                            if (notifiedOrderIds.has(orderId)) {
                                return false;
                            }
                            rememberNotifiedOrder(orderId);
                            return true;
                        });

                        freshOrders.forEach((order, index) => {
                            setTimeout(() => {
                                playChimeSound();
                                speakIndonesianVoice(order.speech_text);
                                showOrderToast(order);
                            }, index * 2500);
                        });

                        if (window.location.pathname.includes('/orders') && !window.location.pathname.includes('/orders/')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 4000);
                        }
                    } else if (data.latest_id) {
                        latestOrderId = data.latest_id;
                    }
                })
                .catch(err => console.log('Check order error:', err))
                .finally(() => {
                    isCheckingNewOrders = false;
                });
            }

            // Poll server every 2 seconds for instant real-time order sound
            setInterval(checkNewOrders, 2000);

            // Instantly check orders when tab becomes active/focused
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    checkNewOrders();
                }
            });
            window.addEventListener('focus', checkNewOrders);
        });
        </script>
        @endif

        <div class="admin-profile">

            <div class="avatar">
                <i class="bi {{ $navbarUser?->role === 'cashier' ? 'bi-calculator-fill' : 'bi-person-fill' }}"></i>
            </div>

            <div>

                <h6>{{ $navbarUser?->name ?? 'Admin' }}</h6>

                <small>{{ $navbarUser?->role === 'cashier' ? 'Kasir Essensia' : 'Admin Essensia' }}</small>

            </div>

        </div>
        <a href="{{ route('logout.get', ['redirect' => $navbarArea]) }}" class="topbar-logout-link" title="Keluar">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>

    </div>

</nav>
