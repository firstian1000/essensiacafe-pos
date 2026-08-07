<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $order->invoice }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/cashier.css') }}?v=1">
</head>
<body class="receipt-body">
    <main class="thermal-receipt">
        <div class="receipt-logo">Essensia<br>Koffie</div>
        <h1>Essensia Koffie</h1>
        <p class="receipt-address">
            Gg. Kurma, Ngijo Gunungpati<br>
            Semarang<br>
            50228 Semarang
        </p>

        <div class="receipt-meta">
            <div><span>Receipt No.</span><strong>{{ $order->invoice }}</strong></div>
            <div><span>{{ $order->created_at->format('d/m/Y H.i.s') }}</span></div>
            <div><span>User</span><strong>Admin Essensia</strong></div>
            <div><span>Order No.</span><strong>{{ $order->customer_name }}</strong></div>
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-items">
            @foreach($order->items as $item)
                <div class="receipt-item-name">{{ strtoupper($item->menu->name ?? 'MENU') }}</div>
                <div class="receipt-item-row">
                    <span>{{ $item->qty }} x Rp{{ number_format($item->price,0,',','.') }}</span>
                    <strong>Rp{{ number_format($item->subtotal,0,',','.') }}</strong>
                </div>
            @endforeach
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-totals">
            <div><span>Items count:</span><strong>{{ $order->items->sum('qty') }}</strong></div>
            <div class="grand"><span>TOTAL:</span><strong>Rp{{ number_format($order->total,0,',','.') }}</strong></div>
            <div><span>{{ strtoupper($order->payment_method ?? 'cash') }}:</span><strong>Rp{{ number_format($order->total,0,',','.') }}</strong></div>
            <div><span>Paid amount:</span><strong>Rp{{ number_format($paidAmount,0,',','.') }}</strong></div>
            <div><span>Change:</span><strong>Rp{{ number_format(max($paidAmount - $order->total, 0),0,',','.') }}</strong></div>
        </div>

        <div class="barcode">
            <span>{{ preg_replace('/[^0-9]/', '', $order->invoice) ?: $order->id }}</span>
        </div>

        <p class="receipt-wifi">
            WIFI: Harina Studio<br>
            Password: -
        </p>
    </main>

    <div class="receipt-actions">
        <a href="{{ route('cashier.index') }}">Kembali ke Kasir</a>
        <button onclick="window.print()">Cetak Nota</button>
    </div>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 350));
    </script>
</body>
</html>
