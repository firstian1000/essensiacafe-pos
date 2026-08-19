<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $order->invoice }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/cashier.css') }}?v=1">
</head>
<body class="receipt-body">
    @php
        $paymentLabels = [
            'cash' => 'Tunai',
            'midtrans' => 'Non Tunai',
        ];
        $paymentLabel = $paymentLabels[$order->payment_method] ?? strtoupper($order->payment_method ?? 'Tunai');
        $serviceLabel = ($order->service_type ?? 'take_away') === 'dine_in' ? 'Dine In' : 'Take Away';
        $wifiUsername = \App\Models\Setting::get('wifi_username', 'Harina Studio');
        $wifiPassword = \App\Models\Setting::get('wifi_password', '-');
    @endphp

    <main class="thermal-receipt">
        <div class="receipt-logo">Esensia<br>Koffie</div>
        <h1>Esensia Koffie</h1>
        <p class="receipt-address">
            Gg. Kurma, Ngijo Gunungpati<br>
            Semarang<br>
            50228 Semarang
        </p>

        <div class="receipt-meta">
            <div><span>Receipt No.</span><strong>{{ $order->invoice }}</strong></div>
            <div><span>{{ $order->created_at->format('d/m/Y H.i.s') }}</span></div>
            <div><span>User</span><strong>Admin Esensia</strong></div>
            <div><span>Order No.</span><strong>{{ $order->customer_name }}</strong></div>
            <div><span>Metode</span><strong>{{ $paymentLabel }}</strong></div>
            <div><span>Layanan</span><strong>{{ $serviceLabel }}</strong></div>
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-items">
            @foreach($order->items as $item)
                <div class="receipt-item-name">{{ strtoupper($item->menu->name ?? 'MENU') }}</div>
                @if($item->variant_name)
                    <div class="receipt-item-note">Varian: {{ $item->variant_name }}</div>
                @endif
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
            <div><span>Metode:</span><strong>{{ $paymentLabel }}</strong></div>
            <div><span>Layanan:</span><strong>{{ $serviceLabel }}</strong></div>
            <div><span>{{ strtoupper($paymentLabel) }}:</span><strong>Rp{{ number_format($order->total,0,',','.') }}</strong></div>
            @if($order->payment_method == 'cash')
            <div><span>Paid amount:</span><strong>Rp{{ number_format($paidAmount,0,',','.') }}</strong></div>
            <div><span>Change:</span><strong>Rp{{ number_format(max($paidAmount - $order->total, 0),0,',','.') }}</strong></div>
            @endif
        </div>

        <div class="barcode">
            <span>{{ preg_replace('/[^0-9]/', '', $order->invoice) ?: $order->id }}</span>
        </div>

        <p class="receipt-wifi">
            WIFI: {{ $wifiUsername ?: '-' }}<br>
            Password: {{ $wifiPassword ?: '-' }}
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
