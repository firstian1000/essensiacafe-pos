<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CafeTable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData($dateString = null)
    {
        $selectedDate = $dateString ? Carbon::parse($dateString) : Carbon::today();

        // Grafik 7 hari terakhir
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = (clone $selectedDate)->subDays($i);
            $labels[] = $tanggal->format('d M');
            $total = Order::whereDate('created_at', $tanggal)
                            ->where('payment_status', 'paid')
                            ->sum('total');
            $data[] = (int) $total;
        }

        // Jumlah pesanan hari ini
        $pesananHariIni = Order::whereDate('created_at', $selectedDate)->count();

        // Status pesanan (overall atau spesifik hari ini? Biasanya untuk dashboard, status pesanan adalah overall,
        // tapi pendapatan hari ini dihitung spesifik)
        $paidOrders    = Order::where('payment_status', 'paid')->count();
        $pendingOrders = Order::where('payment_status', 'pending')->count();
        $failedOrders  = Order::where('payment_status', 'failed')->count();
        $processOrders = Order::where('status', 'processing')->count();

        // Total pendapatan
        $pendapatan = Order::where('payment_status', 'paid')->sum('total');

        // Pendapatan hari ini
        $pendapatanHariIni = Order::whereDate('created_at', $selectedDate)
                                    ->where('payment_status', 'paid')
                                    ->sum('total');

        // Pesanan terbaru
        $pesananTerbaru = Order::with('table')
                                ->latest()
                                ->take(5)
                                ->get();

        // Transaksi gagal / cancelled terbaru
        $recentFailedOrders = Order::with('table')
                                ->where(function($q) {
                                    $q->where('payment_status', 'failed')
                                      ->orWhere('status', 'cancelled');
                                })
                                ->latest()
                                ->take(5)
                                ->get();

        // Menu terlaris
        $menuTerlaris = OrderItem::select(
                            'menu_id',
                            DB::raw('SUM(qty) as total_terjual')
                        )
                        ->with('menu')
                        ->groupBy('menu_id')
                        ->orderByDesc('total_terjual')
                        ->take(5)
                        ->get();

        return [
            'totalKategori'     => Category::count(),
            'totalMenu'         => Menu::count(),
            'totalMeja'         => CafeTable::count(),
            'totalPesanan'      => Order::count(),
            'pesananHariIni'    => $pesananHariIni,
            'pendapatan'        => $pendapatan,
            'pendapatanHariIni' => $pendapatanHariIni,
            'paidOrders'        => $paidOrders,
            'pendingOrders'     => $pendingOrders,
            'failedOrders'      => $failedOrders,
            'processOrders'     => $processOrders,
            'pesananTerbaru'    => $pesananTerbaru,
            'recentFailedOrders'=> $recentFailedOrders,
            'menuTerlaris'      => $menuTerlaris,
            'chartLabels'       => $labels,
            'chartData'         => $data,
        ];
    }
}