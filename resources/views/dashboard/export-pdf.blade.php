<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan dan Revenue Sharing</title>
    <style>
        @page { margin: 28px 30px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #172033;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            background: #fff;
        }
        .page { page-break-after: always; min-height: 1030px; position: relative; }
        .page:last-child { page-break-after: auto; }
        .cover {
            padding: 34px;
            border: 2px solid #173B5F;
            min-height: 980px;
            background: #F8FBFD;
        }
        .brand-row { display: table; width: 100%; margin-bottom: 34px; }
        .brand-left, .brand-right { display: table-cell; vertical-align: top; }
        .brand-right { text-align: right; color: #5D6B7A; }
        .logo-mark {
            display: inline-block;
            width: 46px;
            height: 46px;
            border-radius: 8px;
            background: #173B5F;
            color: #F6B94B;
            text-align: center;
            line-height: 46px;
            font-weight: bold;
            font-size: 20px;
        }
        h1, h2, h3 { margin: 0; }
        .cover h1 {
            margin-top: 110px;
            font-size: 34px;
            line-height: 1.12;
            color: #173B5F;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .cover-subtitle {
            margin-top: 16px;
            width: 70%;
            color: #5D6B7A;
            font-size: 13px;
        }
        .pill {
            display: inline-block;
            margin-top: 24px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #F6B94B;
            color: #173B5F;
            font-weight: bold;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 120px;
            border-spacing: 12px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 16px;
            background: #fff;
            border: 1px solid #D8E0E8;
            border-radius: 8px;
        }
        .summary-card span, .meta-card span { display: block; color: #6B7788; font-size: 10px; text-transform: uppercase; }
        .summary-card strong { display: block; margin-top: 5px; color: #173B5F; font-size: 17px; }
        .header {
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-bottom: 2px solid #173B5F;
        }
        .header h2 { color: #173B5F; font-size: 22px; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: #6B7788; }
        .section-title {
            margin: 18px 0 10px;
            padding: 9px 11px;
            background: #173B5F;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .meta-grid { display: table; width: 100%; border-spacing: 10px; margin-bottom: 10px; }
        .meta-card {
            display: table-cell;
            width: 33.33%;
            padding: 13px;
            border: 1px solid #D8E0E8;
            background: #FAFCFE;
        }
        .meta-card strong { display: block; margin-top: 4px; font-size: 15px; color: #172033; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th {
            padding: 8px 7px;
            background: #EAF1F7;
            color: #173B5F;
            border: 1px solid #C7D3DF;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 7px;
            border: 1px solid #D8E0E8;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #FAFCFE; }
        .money { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .share-box {
            display: table;
            width: 100%;
            border-spacing: 12px;
            margin-top: 12px;
        }
        .share-card {
            display: table-cell;
            width: 50%;
            padding: 18px;
            border: 1px solid #D8E0E8;
            background: #fff;
        }
        .share-card strong { display: block; color: #173B5F; font-size: 24px; margin-top: 8px; }
        .note {
            margin-top: 12px;
            padding: 12px;
            background: #FFF7E6;
            border: 1px solid #F6D38B;
            color: #73510D;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 8px;
            border-top: 1px solid #D8E0E8;
            color: #6B7788;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $dateLabel = $periodLabel ?? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y');
    $paymentLabel = $paymentFilter === 'cash' ? 'Tunai' : ($paymentFilter === 'non_cash' ? 'Non Tunai' : 'Semua Pembayaran');
    $brandLabel = $brandFilter === 'buncha' ? 'Buncha' : ($brandFilter === 'essensia' ? 'Esensia' : 'Semua Brand');
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
@endphp

<section class="page cover">
    <div class="brand-row">
        <div class="brand-left">
            <span class="logo-mark">EK</span>
        </div>
        <div class="brand-right">
            Esensia Koffie<br>
            Dicetak {{ $generatedAt }}
        </div>
    </div>

    <h1>Laporan Penjualan<br>dan Revenue Sharing</h1>
    <p class="cover-subtitle">
        Rekap operasional berdasarkan transaksi yang tercatat pada dashboard admin,
        mencakup ringkasan pendapatan, pengeluaran, pembagian revenue, transaksi, dan detail item.
    </p>
    <span class="pill">{{ $dateLabel }} - {{ $paymentLabel }} - {{ $brandLabel }}</span>

    <div class="summary-grid">
        <div class="summary-card"><span>Pendapatan Paid</span><strong>{{ $rupiah($grossRevenue) }}</strong></div>
        <div class="summary-card"><span>Pengeluaran</span><strong>{{ $rupiah($expenseTotal) }}</strong></div>
        <div class="summary-card"><span>Net Revenue</span><strong>{{ $rupiah($netRevenue) }}</strong></div>
        <div class="summary-card"><span>Pesanan</span><strong>{{ $orders->count() }}</strong></div>
    </div>
    <div class="footer">Halaman 1 / 4</div>
</section>

<section class="page">
    <div class="header">
        <h2>Ringkasan & Revenue Sharing</h2>
        <p>{{ $dateLabel }} - {{ $paymentLabel }} - {{ $brandLabel }}</p>
    </div>

    <div class="meta-grid">
        <div class="meta-card"><span>Total Kategori</span><strong>{{ $totalKategori }}</strong></div>
        <div class="meta-card"><span>Total Menu</span><strong>{{ $totalMenu }}</strong></div>
        <div class="meta-card"><span>Total Meja</span><strong>{{ $totalMeja }}</strong></div>
    </div>
    <div class="meta-grid">
        <div class="meta-card"><span>Transaksi Lunas</span><strong>{{ $orders->where('payment_status', 'paid')->count() }}</strong></div>
        <div class="meta-card"><span>Transaksi Pending</span><strong>{{ $orders->where('payment_status', 'pending')->count() }}</strong></div>
        <div class="meta-card"><span>Transaksi Gagal</span><strong>{{ $orders->whereIn('payment_status', ['failed', 'expired'])->count() }}</strong></div>
    </div>

    <div class="section-title">Perhitungan Revenue</div>
    <table>
        <tbody>
            <tr><td>Pendapatan kotor transaksi lunas</td><td class="money">{{ $rupiah($grossRevenue) }}</td></tr>
            <tr><td>Pengeluaran internal</td><td class="money">{{ $rupiah($expenseTotal) }}</td></tr>
            <tr><td><strong>Net revenue</strong></td><td class="money"><strong>{{ $rupiah($netRevenue) }}</strong></td></tr>
        </tbody>
    </table>

    <div class="share-box">
        <div class="share-card">
            <span>Share Esensia 60%</span>
            <strong>{{ $rupiah($essensiaShare) }}</strong>
        </div>
        <div class="share-card">
            <span>Share Partner 40%</span>
            <strong>{{ $rupiah($partnerShare) }}</strong>
        </div>
    </div>

    <div class="section-title">Penjualan per Kategori</div>
    <table>
        <thead><tr><th>Kategori</th><th class="money">Revenue</th></tr></thead>
        <tbody>
        @forelse($categorySales as $category)
            <tr><td>{{ $category->category_name }}</td><td class="money">{{ $rupiah($category->total_revenue) }}</td></tr>
        @empty
            <tr><td colspan="2">Belum ada penjualan lunas pada filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="note">Catatan: persentase revenue sharing mengikuti format laporan acuan. Ubah persentase di controller jika skema bisnis berbeda.</div>
    <div class="footer">Halaman 2 / 4</div>
</section>

<section class="page">
    <div class="header">
        <h2>Daftar Transaksi</h2>
        <p>{{ $dateLabel }} - {{ $orders->count() }} transaksi</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Meja</th>
                <th>Waktu</th>
                <th class="money">Total</th>
                <th>Metode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->invoice }}</td>
                <td>{{ $order->customer_name ?: '-' }}</td>
                <td>{{ $order->table?->display_name ?: '-' }}</td>
                <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                <td class="money">{{ $rupiah($order->total) }}</td>
                <td>{{ $order->payment_method === 'cash' ? 'Tunai' : 'Non Tunai' }}</td>
                <td>{{ ucfirst($order->payment_status ?: '-') }}</td>
            </tr>
        @empty
            <tr><td colspan="7">Tidak ada transaksi pada filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="footer">Halaman 3 / 4</div>
</section>

<section class="page">
    <div class="header">
        <h2>Detail Item Penjualan</h2>
        <p>Rincian menu terjual berdasarkan transaksi pada filter laporan.</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Menu</th>
                <th class="center">Qty</th>
                <th class="money">Harga</th>
                <th class="money">Subtotal</th>
                <th>Customer</th>
            </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $order->invoice }}</td>
                    <td>{{ $item->menu?->name ?: '-' }}</td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="money">{{ $rupiah($item->price) }}</td>
                    <td class="money">{{ $rupiah($item->subtotal) }}</td>
                    <td>{{ $order->customer_name ?: '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr><td colspan="6">Tidak ada detail item pada filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="footer">Halaman 4 / 4</div>
</section>
</body>
</html>
