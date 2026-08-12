<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #9CA3AF; padding: 6px; }
        th { background: #D9EAF7; font-weight: bold; }
        .title { font-size: 18px; font-weight: bold; }
        .section { background: #2E7DB8; color: #fff; font-weight: bold; }
        .money { mso-number-format:"\#\,\#\#0"; }
    </style>
</head>
<body>
<table>
    <tr><td colspan="8" class="title">Rekap Data Essensia Koffie</td></tr>
    <tr><td colspan="8">Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</td></tr>
    <tr><td colspan="8">Filter: Pembayaran {{ ucfirst(str_replace('_', ' ', $paymentFilter ?? 'all')) }} | Brand {{ ucfirst($brandFilter ?? 'all') }}</td></tr>
    <tr><td colspan="8">Dicetak: {{ $generatedAt }}</td></tr>
    <tr><td colspan="8"></td></tr>

    <tr><td colspan="8" class="section">Ringkasan</td></tr>
    <tr>
        <th>Total Kategori</th>
        <th>Total Menu</th>
        <th>Total Meja</th>
        <th>Total Pesanan</th>
        <th>Pengeluaran Internal</th>
        <th>Pendapatan Paid</th>
        <th>Pendapatan Tanggal Ini</th>
        <th>Pending</th>
    </tr>
    <tr>
        <td>{{ $totalKategori }}</td>
        <td>{{ $totalMenu }}</td>
        <td>{{ $totalMeja }}</td>
        <td>{{ $totalPesanan }}</td>
        <td class="money">{{ $totalPengeluaran }}</td>
        <td class="money">{{ $pendapatan }}</td>
        <td class="money">{{ $pendapatanHariIni }}</td>
        <td>{{ $pendingOrders }}</td>
    </tr>
    <tr><td colspan="8"></td></tr>

    <tr><td colspan="8" class="section">Daftar Pesanan</td></tr>
    <tr>
        <th>Invoice</th>
        <th>Customer</th>
        <th>Meja</th>
        <th>Waktu</th>
        <th>Total</th>
        <th>Metode Bayar</th>
        <th>Status Bayar</th>
        <th>Status Pesanan</th>
    </tr>
    @forelse($orders as $order)
        <tr>
            <td>{{ $order->invoice }}</td>
            <td>{{ $order->customer_name ?: '-' }}</td>
            <td>{{ $order->table?->display_name ?: '-' }}</td>
            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
            <td class="money">{{ $order->total }}</td>
            <td>{{ strtoupper($order->payment_method ?: '-') }}</td>
            <td>{{ ucfirst($order->payment_status ?: '-') }}</td>
            <td>{{ ucfirst($order->status ?: '-') }}</td>
        </tr>
    @empty
        <tr><td colspan="8">Tidak ada pesanan pada tanggal ini.</td></tr>
    @endforelse
    <tr><td colspan="8"></td></tr>

    <tr><td colspan="8" class="section">Detail Item Pesanan</td></tr>
    <tr>
        <th>Invoice</th>
        <th>Menu</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Subtotal</th>
        <th>Customer</th>
        <th>Meja</th>
        <th>Waktu</th>
    </tr>
    @foreach($orders as $order)
        @foreach($order->items as $item)
            <tr>
                <td>{{ $order->invoice }}</td>
                <td>{{ $item->menu?->name ?: '-' }}</td>
                <td>{{ $item->qty }}</td>
                <td class="money">{{ $item->price }}</td>
                <td class="money">{{ $item->subtotal }}</td>
                <td>{{ $order->customer_name ?: '-' }}</td>
                <td>{{ $order->table?->display_name ?: '-' }}</td>
                <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    @endforeach
</table>
</body>
</html>
