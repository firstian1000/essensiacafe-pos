<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CafeTable;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData($dateString = null, string $paymentFilter = 'all', string $brandFilter = 'all', ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $selectedDate = $dateString ? Carbon::parse($dateString) : Carbon::today();
        $startDate = $startDate ? $startDate->copy()->startOfDay() : $selectedDate->copy()->startOfDay();
        $endDate = $endDate ? $endDate->copy()->endOfDay() : $selectedDate->copy()->endOfDay();
        $paymentFilter = in_array($paymentFilter, ['all', 'cash', 'non_cash'], true) ? $paymentFilter : 'all';
        $brandFilter = in_array($brandFilter, ['all', 'essensia', 'buncha'], true) ? $brandFilter : 'all';

        $labels = [];
        $data = [];
        $filteredRevenueData = [];

        if ($startDate->isSameDay($endDate)) {
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = (clone $selectedDate)->subDays($i);
                $labels[] = $tanggal->format('d M');
                $filteredTotal = $this->filteredRevenueForDate($tanggal, $paymentFilter, $brandFilter);
                $data[] = $filteredTotal;
                $filteredRevenueData[] = $filteredTotal;
            }
        } elseif ($startDate->isSameYear($endDate) && $startDate->month === 1 && $startDate->day === 1 && $endDate->month === 12 && $endDate->day === 31) {
            for ($month = 1; $month <= 12; $month++) {
                $monthStart = $startDate->copy()->month($month)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $labels[] = $monthStart->format('M');
                $filteredTotal = $this->filteredRevenueForRange($monthStart, $monthEnd, $paymentFilter, $brandFilter);
                $data[] = $filteredTotal;
                $filteredRevenueData[] = $filteredTotal;
            }
        } else {
            $tanggal = $startDate->copy();
            while ($tanggal->lte($endDate)) {
                $labels[] = $tanggal->format('d M');
                $filteredTotal = $this->filteredRevenueForDate($tanggal, $paymentFilter, $brandFilter);
                $data[] = $filteredTotal;
                $filteredRevenueData[] = $filteredTotal;
                $tanggal->addDay();
            }
        }

        // Jumlah pesanan hari ini
        $pesananHariIni = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // Status pesanan (overall atau spesifik hari ini? Biasanya untuk dashboard, status pesanan adalah overall,
        // tapi pendapatan hari ini dihitung spesifik)
        $statusBaseQuery = $this->filteredOrderQuery($paymentFilter, $brandFilter)
            ->whereBetween('created_at', [$startDate, $endDate]);
        $paidOrders = (clone $statusBaseQuery)->where('payment_status', 'paid')->count();
        $pendingOrders = (clone $statusBaseQuery)->where('payment_status', 'pending')->count();
        $failedOrders = (clone $statusBaseQuery)->where('payment_status', 'failed')->count();
        $processOrders = (clone $statusBaseQuery)->where('status', 'processing')->count();
        $filteredStatusTotal = $paidOrders + $pendingOrders + $failedOrders + $processOrders;

        $pendapatan = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Pendapatan hari ini
        $pendapatanHariIni = Order::whereBetween('created_at', [$startDate, $endDate])
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
                            DB::raw('SUM(qty) as total_terjual'),
                            DB::raw('SUM(subtotal) as total_pendapatan')
                        )
                        ->with('menu')
                        ->groupBy('menu_id')
                        ->orderByDesc('total_terjual')
                        ->take(5)
                        ->get();

        $categorySales = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menus', 'order_items.menu_id', '=', 'menus.id')
            ->leftJoin('categories', 'menus.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name")
            ->selectRaw('SUM(order_items.subtotal) as total_revenue')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid');

        $this->applyItemFilters($categorySales, $paymentFilter, $brandFilter);

        $categorySales = $categorySales
            ->groupBy('category_name')
            ->orderByDesc('total_revenue')
            ->take(6)
            ->get();

        if (Schema::hasTable('stock_items')) {
            $stockMenus = StockItem::query()
                ->whereNotNull('stock')
                ->orderBy('stock')
                ->orderBy('name')
                ->take(6)
                ->get();

            $lowStockCount = StockItem::whereNotNull('stock')->where('stock', '>', 0)->where('stock', '<=', 5)->count();
            $emptyStockCount = StockItem::whereNotNull('stock')->where('stock', '<=', 0)->count();
        } else {
            $stockMenus = collect();
            $lowStockCount = 0;
            $emptyStockCount = 0;
        }
        $totalPengeluaran = Schema::hasTable('expenses')
            ? (int) Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->sum('amount')
            : 0;
        $keuntungan = (int) $pendapatan - $totalPengeluaran;

        return [
            'totalKategori'     => Category::count(),
            'totalMenu'         => Menu::count(),
            'totalMeja'         => CafeTable::count(),
            'totalPesanan'      => $pesananHariIni,
            'pesananHariIni'    => $pesananHariIni,
            'pendapatan'        => $pendapatan,
            'pendapatanHariIni' => $pendapatanHariIni,
            'paidOrders'        => $paidOrders,
            'pendingOrders'     => $pendingOrders,
            'failedOrders'      => $failedOrders,
            'processOrders'     => $processOrders,
            'filteredStatusTotal' => $filteredStatusTotal,
            'pesananTerbaru'    => $pesananTerbaru,
            'recentFailedOrders'=> $recentFailedOrders,
            'menuTerlaris'      => $menuTerlaris,
            'categorySales'     => $categorySales,
            'stockMenus'        => $stockMenus,
            'lowStockCount'     => $lowStockCount,
            'emptyStockCount'   => $emptyStockCount,
            'totalPengeluaran'  => $totalPengeluaran,
            'keuntungan'        => $keuntungan,
            'chartLabels'       => $labels,
            'chartData'         => $data,
            'filteredRevenueData'=> $filteredRevenueData,
            'filteredRevenueTotal' => array_sum($filteredRevenueData),
        ];
    }

    private function filteredRevenueForDate(Carbon $date, string $paymentFilter, string $brandFilter): int
    {
        return $this->filteredRevenueForRange($date->copy()->startOfDay(), $date->copy()->endOfDay(), $paymentFilter, $brandFilter);
    }

    private function filteredRevenueForRange(Carbon $startDate, Carbon $endDate, string $paymentFilter, string $brandFilter): int
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menus', 'order_items.menu_id', '=', 'menus.id')
            ->leftJoin('categories', 'menus.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid');

        $this->applyItemFilters($query, $paymentFilter, $brandFilter);

        return (int) $query->sum('order_items.subtotal');
    }

    private function applyItemFilters($query, string $paymentFilter, string $brandFilter): void
    {
        if ($paymentFilter === 'cash') {
            $query->where('orders.payment_method', 'cash');
        } elseif ($paymentFilter === 'non_cash') {
            $query->where('orders.payment_method', '!=', 'cash');
        }

        if ($brandFilter === 'buncha') {
            $query->whereRaw('LOWER(COALESCE(categories.name, ?)) LIKE ?', ['', '%dimsum%']);
        } elseif ($brandFilter === 'essensia') {
            $query->whereRaw('LOWER(COALESCE(categories.name, ?)) NOT LIKE ?', ['', '%dimsum%']);
        }
    }

    private function filteredOrderQuery(string $paymentFilter, string $brandFilter)
    {
        $query = Order::query();

        if ($paymentFilter === 'cash') {
            $query->where('payment_method', 'cash');
        } elseif ($paymentFilter === 'non_cash') {
            $query->where('payment_method', '!=', 'cash');
        }

        if ($brandFilter === 'buncha') {
            $query->whereHas('items.menu.category', function ($category) {
                $category->whereRaw('LOWER(name) LIKE ?', ['%dimsum%']);
            });
        } elseif ($brandFilter === 'essensia') {
            $query->whereDoesntHave('items.menu.category', function ($category) {
                $category->whereRaw('LOWER(name) LIKE ?', ['%dimsum%']);
            });
        }

        return $query;
    }
}
