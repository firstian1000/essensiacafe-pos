<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('table', 'items.menu');

        // Filter tanggal
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $dateFrom = $request->get('date_from', $selectedDate);
        $dateTo   = $request->get('date_to',   $selectedDate);

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);

        // Filter status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter metode pembayaran
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->latest()->paginate(15);

        // Ringkasan keuangan untuk periode tersebut
        $summaryQuery = Order::whereDate('created_at', '>=', $dateFrom)
                             ->whereDate('created_at', '<=', $dateTo);

        $totalPendapatan   = (clone $summaryQuery)->where('payment_status', 'paid')->sum('total');
        $totalTransaksi    = (clone $summaryQuery)->count();
        $transaksiPaid     = (clone $summaryQuery)->where('payment_status', 'paid')->count();
        $transaksiPending  = (clone $summaryQuery)->where('payment_status', 'pending')->count();
        $transaksiGagal    = (clone $summaryQuery)->where('payment_status', 'failed')->count();

        // Rekap per metode pembayaran
        $rekapMetode = (clone $summaryQuery)
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(total) as total_pendapatan'))
            ->groupBy('payment_method')
            ->get();

        return view('payments.index', compact(
            'orders',
            'dateFrom',
            'dateTo',
            'totalPendapatan',
            'totalTransaksi',
            'transaksiPaid',
            'transaksiPending',
            'transaksiGagal',
            'rekapMetode'
        ));
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('payments.show', compact('order'));
    }

    public function qris(Order $order)
    {
        abort_unless($order->payment_method === 'midtrans' && $order->snap_token, 404);

        $order->load(['table', 'items.menu']);

        return view('payments.qris', compact('order'));
    }

    public function receipt(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('payments.receipt', compact('order'));
    }
}
