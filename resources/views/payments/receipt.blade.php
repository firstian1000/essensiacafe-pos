<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota #{{ $order->invoice }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
/* ================================================================
   SCREEN STYLES  — tampil seperti panel kasir (gambar 1)
================================================================ */
*, *::before, *::after { box-sizing: border-box; }

body {
    margin: 0;
    padding: 0;
    background: #F0F4F8;
    font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
    color: #0F172A;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* ── Top success bar ── */
.success-bar {
    width: 100%;
    background: linear-gradient(90deg, #16a34a, #15803d);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .2px;
}

.success-bar i { font-size: 18px; }

/* ── Page wrapper ── */
.page-wrap {
    width: 100%;
    max-width: 480px;
    padding: 28px 16px 48px;
}

/* ── Card  (mirip .cashier-cart-panel) ── */
.nota-card {
    background: #fff;
    border: 1px solid #E6EDF5;
    border-radius: 22px;
    box-shadow: 0 12px 35px rgba(31,41,55,.07);
    overflow: hidden;
    animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both;
}

@keyframes fadeUp {
    from { opacity:0; transform:translateY(18px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── Card header ── */
.nota-head {
    padding: 18px 22px 14px;
    border-bottom: 1px solid #E6EDF5;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.nota-head-left h2 {
    margin: 0 0 3px;
    font-size: 22px;
    font-weight: 950;
    color: #0F172A;
}

.nota-head-left p {
    margin: 0;
    font-size: 13px;
    color: #64748B;
    font-weight: 600;
}

.btn-print-head {
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 14px;
    background: #EAF4FB;
    color: #2E7DB8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .15s;
}

.btn-print-head:hover { background: #2E7DB8; color: #fff; }
.btn-print-head i { font-size: 20px; }

/* ── Customer + Metode row ── */
.nota-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    padding: 16px 22px;
    border-bottom: 1px solid #E6EDF5;
}

.nota-field label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 12px;
    font-weight: 900;
    color: #475569;
}

.nota-field .field-val {
    height: 42px;
    border: 1px solid #DDE7F1;
    border-radius: 14px;
    padding: 0 13px;
    display: flex;
    align-items: center;
    font-size: 14px;
    font-weight: 700;
    color: #0F172A;
    background: #F8FBFF;
}

.badge-cash {
    background: #FEF3C7;
    color: #92400E;
    border-radius: 999px;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 800;
}

/* ── Items list ── */
.nota-items {
    padding: 0 22px;
    max-height: 320px;
    overflow-y: auto;
}

.nota-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 0;
    border-bottom: 1px solid #F1F5F9;
}

.nota-item:last-child { border-bottom: none; }

.nota-item-left strong {
    display: block;
    font-size: 14px;
    font-weight: 800;
    color: #0F172A;
}

.nota-item-left span {
    font-size: 12px;
    color: #64748B;
    font-weight: 600;
}

.nota-item-price {
    font-size: 14px;
    font-weight: 800;
    color: #1a3a5c;
    white-space: nowrap;
}

.nota-empty {
    padding: 28px 0;
    text-align: center;
    color: #64748B;
    font-weight: 700;
    font-size: 14px;
    border: 1px dashed #CBD5E1;
    border-radius: 16px;
    margin: 16px 0;
}

/* ── Summary block ── */
.nota-summary {
    background: #F8FBFF;
    border: 1px solid #E6EDF5;
    border-radius: 16px;
    margin: 0 22px 16px;
    padding: 14px 16px;
    display: grid;
    gap: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
}

.summary-row.total {
    font-size: 17px;
    font-weight: 900;
    color: #0F172A;
    padding-top: 10px;
    border-top: 1px solid #DDE7F1;
}

.summary-row.total strong { color: #2E7DB8; }

.summary-row.lunas-row strong {
    background: #dcfce7;
    color: #16a34a;
    border-radius: 999px;
    padding: 3px 12px;
    font-size: 12px;
    font-weight: 800;
}

/* ── Action buttons ── */
.nota-footer {
    padding: 0 22px 22px;
    display: flex;
    gap: 10px;
}

.btn-nota {
    flex: 1;
    height: 50px;
    border: 0;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: transform .12s, box-shadow .12s;
}

.btn-nota:active { transform: scale(.97); }

.btn-nota-print {
    background: #2E7DB8;
    color: #fff;
    box-shadow: 0 10px 24px rgba(46,125,184,.28);
}

.btn-nota-print:hover { background: #236895; }

.btn-nota-back {
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #DDE7F1;
}

.btn-nota-back:hover { background: #E2E8F0; }


/* ================================================================
   PRINT STYLES  — thermal 58 mm (gambar 2)
================================================================ */
@media print {

    .success-bar,
    .nota-card,
    body { display: block !important; background: #fff !important; padding: 0 !important; margin: 0 !important; }

    .success-bar, .nota-head, .nota-fields, .nota-items,
    .nota-summary, .nota-footer, .nota-empty, .page-wrap,
    .nota-card { display: none !important; }

    .thermal-print { display: block !important; }
}

/* Hidden on screen, shown when printing */
.thermal-print { display: none; }

@page {
    /* thermal 58mm roll */
    size: 58mm auto;
    margin: 0;
}

/* Thermal receipt inner */
.thermal-inner {
    width: 58mm;
    font-family: 'Courier New', Courier, monospace;
    min-height: 100vh;
    font-size: 10px;
    line-height: 1.18;
    color: #000;
    background: #fff;
    padding: 4mm 3mm;
    margin: 0 auto;
}

.t-logo {
    text-align: center;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: -1px;
    line-height: .85;
    margin: 2mm 0;
}

.t-title {
    text-align: center;
    font-size: 14px;
    font-weight: 900;
    margin: 2mm 0 1mm;
}

.t-addr {
    text-align: center;
    font-size: 9px;
    line-height: 1.45;
    margin: 0 0 2mm;
}

.t-line {
    border: none;
    border-top: 1px dashed #000;
    margin: 2mm 0;
}

.t-meta {
    display: grid;
    grid-template-columns: 1fr;
    gap: .5mm;
}

.t-meta-row {
    display: flex;
    justify-content: space-between;
    gap: 4px;
    font-size: 10px;
}

.t-meta-row span { color: #000; }
.t-meta-row strong { font-weight: 700; text-align: right; word-break: break-all; }

.t-items { margin: 1mm 0; }

.t-item-name {
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    margin-top: 1mm;
}

.t-item-note {
    font-size: 9px;
    font-weight: 700;
    margin: .3mm 0;
}

.t-item-row {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
}

.t-totals { margin-top: 1mm; }

.t-total-row {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    margin: .4mm 0;
}

.t-grand {
    border-top: 1px dashed #000;
    margin-top: 1.5mm;
    padding-top: 1.5mm;
    font-size: 12px;
    font-weight: 900;
}

.t-barcode {
    height: 18mm;
    margin: 3mm 0 2mm;
    background: repeating-linear-gradient(
        90deg,
        #000 0, #000 1px,
        #fff 1px, #fff 3px,
        #000 3px, #000 5px,
        #fff 5px, #fff 7px
    );
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 1mm;
}

.t-barcode span {
    background: #fff;
    padding: 0 2px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .5px;
}

.t-wifi {
    text-align: center;
    font-size: 10px;
    font-weight: 900;
    margin: 2mm 0 0;
    line-height: 1.18;
}

    </style>
</head>
<body>

@php
    $paymentLabels = [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'ewallet' => 'E-Wallet',
        'card' => 'E-Wallet',
        'midtrans' => 'Non Tunai',
    ];
    $paymentLabel = $paymentLabels[$order->payment_method] ?? strtoupper($order->payment_method ?? 'Tunai');
    $serviceLabel = ($order->service_type ?? 'take_away') === 'dine_in' ? 'Dine In' : 'Take Away';
    $wifiUsername = \App\Models\Setting::get('wifi_username', 'Harina Studio');
    $wifiPassword = \App\Models\Setting::get('wifi_password', '-');
@endphp

{{-- ====================================================
     SUCCESS BAR (screen only)
===================================================== --}}
<div class="success-bar">
    <i class="bi bi-check-circle-fill"></i>
    Pembayaran {{ $paymentLabel }} Dikonfirmasi Lunas - Nota siap dicetak
</div>

{{-- ====================================================
     SCREEN CARD (mirip panel kasir)
===================================================== --}}
<div class="page-wrap">
    <div class="nota-card">

        {{-- Header --}}
        <div class="nota-head">
            <div class="nota-head-left">
                <h2>Nota Kasir</h2>
                <p>Pembayaran {{ $paymentLabel }} telah dikonfirmasi lunas</p>
            </div>
            <button class="btn-print-head" onclick="window.print()" title="Cetak Nota">
                <i class="bi bi-printer-fill"></i>
            </button>
        </div>

        {{-- Customer + Metode --}}
        <div class="nota-fields">
            <div class="nota-field">
                <label>
                    Customer
                    <div class="field-val">{{ $order->customer_name ?: 'Guest' }}</div>
                </label>
            </div>
            <div class="nota-field">
                <label>
                    Metode
                    <div class="field-val">
                        @if($order->payment_method == 'cash')
                            <span class="badge-cash">
                                <i class="bi bi-cash-coin" style="margin-right:4px"></i>Tunai
                            </span>
                        @else
                            <span class="badge-cash" style="background:#e0f2fe; color:#0369a1;">
                                <i class="bi bi-qr-code" style="margin-right:4px"></i>{{ $paymentLabel }}
                            </span>
                        @endif
                    </div>
                </label>
            </div>
            <div class="nota-field">
                <label>
                    Layanan
                    <div class="field-val">
                        <span class="badge-cash" style="background:#EAF4FB; color:#2E7DB8;">
                            <i class="bi {{ $serviceLabel === 'Dine In' ? 'bi-cup-hot' : 'bi-bag' }}" style="margin-right:4px"></i>{{ $serviceLabel }}
                        </span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Items --}}
        <div class="nota-items">
            @forelse($order->items as $item)
                <div class="nota-item">
                    <div class="nota-item-left">
                        <strong>{{ $item->menu->name ?? 'Menu Dihapus' }}</strong>
                        <span>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="nota-item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="nota-empty">
                    <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4"></i>
                    Tidak ada item pesanan.
                </div>
            @endforelse
        </div>

        {{-- Summary --}}
        <div class="nota-summary" style="margin-top:16px;">
            <div class="summary-row">
                <span>Total Item</span>
                <strong>{{ $order->items->sum('qty') }}</strong>
            </div>
            <div class="summary-row">
                <span>Invoice</span>
                <strong>{{ $order->invoice }}</strong>
            </div>
            <div class="summary-row">
                <span>Meja</span>
                <strong>{{ optional($order->table)->table_number ?? 'Kasir' }}</strong>
            </div>
            <div class="summary-row">
                <span>Metode</span>
                <strong>{{ $paymentLabel }}</strong>
            </div>
            <div class="summary-row">
                <span>Layanan</span>
                <strong>{{ $serviceLabel }}</strong>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <strong style="font-size:18px; color:#0F172A;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>

            @if($order->payment_method == 'cash')
                {{-- Field Uang Diterima (Manual Input) --}}
                <div class="uang-diterima-section" style="margin: 14px 0 6px;">
                    <label style="display:block; font-size:13px; font-weight:800; color:#475569; margin-bottom:6px;">Uang Diterima</label>
                    <div style="display:flex; align-items:center; border:1px solid #CBD5E1; border-radius:14px; background:#fff; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,.03);">
                        <span style="padding:0 14px; background:#EAF4FB; color:#2E7DB8; font-weight:900; font-size:14px; height:46px; display:flex; align-items:center; border-right:1px solid #CBD5E1;">Rp</span>
                        <input type="number" id="input_paid_amount" value="{{ request('paid', $order->total) }}" min="0" step="500" placeholder="Masukkan nominal" style="flex:1; height:46px; border:0; padding:0 14px; font-size:17px; font-weight:900; color:#0F172A; outline:none;" oninput="calculateChange()" autofocus>
                    </div>

                    {{-- Quick preset buttons --}}
                    <div style="display:flex; gap:6px; margin-top:8px; flex-wrap:wrap;">
                        <button type="button" onclick="setPaid({{ $order->total }})" style="border:1px solid #DDE7F1; background:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:800; color:#475569; cursor:pointer;">Uang Pas</button>
                        <button type="button" onclick="setPaid(10000)" style="border:1px solid #DDE7F1; background:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:800; color:#475569; cursor:pointer;">10.000</button>
                        <button type="button" onclick="setPaid(20000)" style="border:1px solid #DDE7F1; background:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:800; color:#475569; cursor:pointer;">20.000</button>
                        <button type="button" onclick="setPaid(50000)" style="border:1px solid #DDE7F1; background:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:800; color:#475569; cursor:pointer;">50.000</button>
                        <button type="button" onclick="setPaid(100000)" style="border:1px solid #DDE7F1; background:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:800; color:#475569; cursor:pointer;">100.000</button>
                    </div>
                </div>

                {{-- Kembalian (Otomatis) --}}
                <div class="summary-row kembalian-row" style="font-size:15px; font-weight:900; margin-top:8px; padding-top:10px; border-top:1px dashed #DDE7F1;">
                    <span>Kembalian</span>
                    <strong id="display_change" style="color:#16a34a; font-size:18px;">Rp 0</strong>
                </div>
            @endif

            <div class="summary-row lunas-row" style="margin-top:8px;">
                <span>Status</span>
                <strong><i class="bi bi-check-circle-fill"></i> LUNAS</strong>
            </div>
        </div>

        {{-- Footer buttons --}}
        <div class="nota-footer">
            <a href="{{ route('cashier.index') }}" class="btn-nota btn-nota-back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
            <button onclick="handleCetakNota()" class="btn-nota btn-nota-print">
                <i class="bi bi-printer-fill"></i>
                Cetak Nota
            </button>
        </div>

    </div>
</div>


{{-- ====================================================
     THERMAL PRINT OUTPUT (58 mm — persis gambar 2)
     Hidden on screen, shown only when printing
===================================================== --}}
<div class="thermal-print">
    <div class="thermal-inner thermal-receipt-unified">

        {{-- Logo --}}
        <div class="t-logo">Essensia<br>Koffie</div>
        <div class="t-title">Essensia Koffie</div>
        <div class="t-addr">
            Gg. Kurma, Ngijo Gunungpati<br>
            Semarang<br>
            50228 Semarang
        </div>

        <hr class="t-line">

        {{-- Meta --}}
        <div class="t-meta">
            <div class="t-meta-row">
                <span>Receipt No.</span>
                <strong>{{ $order->invoice }}</strong>
            </div>
            <div class="t-meta-row">
                <span>{{ $order->created_at->format('d/m/Y H.i.s') }}</span>
                <strong></strong>
            </div>
            <div class="t-meta-row">
                <span>User</span>
                <strong>Admin Essensia</strong>
            </div>
            <div class="t-meta-row">
                <span>Order No.</span>
                <strong>{{ $order->customer_name ?: 'Customer Kasir' }}</strong>
            </div>
            <div class="t-meta-row">
                <span>Metode</span>
                <strong>{{ $paymentLabel }}</strong>
            </div>
            <div class="t-meta-row">
                <span>Layanan</span>
                <strong>{{ $serviceLabel }}</strong>
            </div>
        </div>

        <hr class="t-line">

        {{-- Items --}}
        <div class="t-items">
            @foreach($order->items as $item)
                <div class="t-item-name">{{ strtoupper($item->menu->name ?? 'MENU') }}</div>
                @if($item->variant_name)
                    <div class="t-item-note">Varian: {{ $item->variant_name }}</div>
                @endif
                <div class="t-item-row">
                    <span>{{ $item->qty }} x Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                    <strong>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>

        <hr class="t-line">

        {{-- Totals --}}
        <div class="t-totals">
            <div class="t-total-row">
                <span>Items count:</span>
                <strong>{{ $order->items->sum('qty') }}</strong>
            </div>
            <div class="t-total-row t-grand">
                <span>TOTAL:</span>
                <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
            <div class="t-total-row">
                <span>Metode:</span>
                <strong>{{ $paymentLabel }}</strong>
            </div>
            <div class="t-total-row">
                <span>Layanan:</span>
                <strong>{{ $serviceLabel }}</strong>
            </div>
            <div class="t-total-row">
                <span>{{ strtoupper($paymentLabel) }}:</span>
                <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
            @if($order->payment_method == 'cash')
            <div class="t-total-row">
                <span>Paid amount:</span>
                <strong id="t_paid_amount">Rp{{ number_format(request('paid', $order->total), 0, ',', '.') }}</strong>
            </div>
            <div class="t-total-row">
                <span>Change:</span>
                <strong id="t_change_amount">Rp{{ number_format(max(0, request('paid', $order->total) - $order->total), 0, ',', '.') }}</strong>
            </div>
            @endif
        </div>

        {{-- Barcode --}}
        <div class="t-barcode">
            <span>{{ preg_replace('/[^0-9]/', '', $order->invoice) ?: $order->id }}</span>
        </div>

        {{-- WiFi --}}
        <div class="t-wifi">
            WIFI: {{ $wifiUsername ?: '-' }}<br>
            Password: {{ $wifiPassword ?: '-' }}
        </div>

    </div>
</div>


<script>
    const orderTotal = {{ $order->total }};

    function formatRupiah(number) {
        return 'Rp' + new Intl.NumberFormat('id-ID').format(Math.round(number));
    }

    function calculateChange() {
        const inputEl = document.getElementById('input_paid_amount');
        if (!inputEl) return;
        const paidVal = parseFloat(inputEl.value) || 0;
        const change = Math.max(0, paidVal - orderTotal);

        const changeEl = document.getElementById('display_change');
        if (changeEl) {
            changeEl.innerText = formatRupiah(change);
        }

        const tPaidEl = document.getElementById('t_paid_amount');
        const tChangeEl = document.getElementById('t_change_amount');
        if (tPaidEl) {
            tPaidEl.innerText = formatRupiah(paidVal);
        }
        if (tChangeEl) {
            tChangeEl.innerText = formatRupiah(change);
        }
    }

    function setPaid(amount) {
        const inputEl = document.getElementById('input_paid_amount');
        if (inputEl) {
            inputEl.value = amount;
            calculateChange();
        }
    }

    function handleCetakNota() {
        calculateChange();
        window.print();
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateChange();
    });

    @if($order->payment_method != 'cash' || request()->has('paid'))
        window.addEventListener('load', () => setTimeout(() => handleCetakNota(), 450));
    @endif
</script>

</body>
</html>
