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
        $data = $this->dashboardService->getDashboardData($date);
        $data['selectedDate'] = $date;

        return view('dashboard.index', $data);
    }


    public function export(Request $request): StreamedResponse
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $data = $this->dashboardService->getDashboardData($date);
        $orders = Order::with(['table', 'items.menu'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $filename = 'rekap-essensia-koffie-' . $date . '.xls';

        return response()->streamDownload(function () use ($data, $orders, $date) {
            echo view('dashboard.export', [
                ...$data,
                'orders' => $orders,
                'selectedDate' => $date,
                'generatedAt' => now()->format('d/m/Y H:i'),
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }
}

