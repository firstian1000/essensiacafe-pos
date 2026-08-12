<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $paymentFilter = $request->get('payment_filter', 'all');
        $brandFilter = $request->get('brand_filter', 'all');

        $data = $this->dashboardService->getDashboardData($date, $paymentFilter, $brandFilter);
        $data['selectedDate'] = $date;
        $data['paymentFilter'] = $paymentFilter;
        $data['brandFilter'] = $brandFilter;

        return view('dashboard.index', $data);
    }


    public function export(Request $request): StreamedResponse
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $paymentFilter = $request->get('payment_filter', 'all');
        $brandFilter = $request->get('brand_filter', 'all');

        $data = $this->dashboardService->getDashboardData($date, $paymentFilter, $brandFilter);
        $ordersQuery = Order::with(['table', 'items.menu.category'])
            ->whereDate('created_at', $date)
            ->latest();

        if ($paymentFilter === 'cash') {
            $ordersQuery->where('payment_method', 'cash');
        } elseif ($paymentFilter === 'non_cash') {
            $ordersQuery->where('payment_method', '!=', 'cash');
        }

        if ($brandFilter === 'buncha') {
            $ordersQuery->whereHas('items.menu.category', function ($category) {
                $category->whereRaw('LOWER(name) LIKE ?', ['%dimsum%']);
            });
        } elseif ($brandFilter === 'essensia') {
            $ordersQuery->whereDoesntHave('items.menu.category', function ($category) {
                $category->whereRaw('LOWER(name) LIKE ?', ['%dimsum%']);
            });
        }

        $orders = $ordersQuery->get();

        $filename = 'rekap-essensia-koffie-' . $date . '.xls';

        return response()->streamDownload(function () use ($data, $orders, $date) {
            echo view('dashboard.export', [
                ...$data,
                'orders' => $orders,
                'selectedDate' => $date,
                'paymentFilter' => $paymentFilter,
                'brandFilter' => $brandFilter,
                'generatedAt' => now()->format('d/m/Y H:i'),
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }
}
