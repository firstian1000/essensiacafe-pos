<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
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


    public function export(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $paymentFilter = $request->get('payment_filter', 'all');
        $brandFilter = $request->get('brand_filter', 'all');
        $format = $request->get('format', 'excel');

        $exportData = $this->buildExportData($date, $paymentFilter, $brandFilter);

        if ($format === 'pdf') {
            $filename = 'laporan-essensia-koffie-' . $date . '.pdf';

            return Pdf::loadView('dashboard.export-pdf', $exportData)
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        $filename = 'rekap-essensia-koffie-' . $date . '.xls';

        return response()->streamDownload(function () use ($exportData) {
            echo view('dashboard.export', $exportData)->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function buildExportData(string $date, string $paymentFilter, string $brandFilter): array
    {
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
        $paidOrders = $orders->where('payment_status', 'paid');
        $grossRevenue = (int) $paidOrders->sum('total');
        $expenseTotal = (int) ($data['totalPengeluaran'] ?? 0);
        $netRevenue = $grossRevenue - $expenseTotal;
        $essensiaShare = (int) round($netRevenue * 0.6);
        $partnerShare = $netRevenue - $essensiaShare;

        return [
            ...$data,
            'orders' => $orders,
            'selectedDate' => $date,
            'paymentFilter' => $paymentFilter,
            'brandFilter' => $brandFilter,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'grossRevenue' => $grossRevenue,
            'expenseTotal' => $expenseTotal,
            'netRevenue' => $netRevenue,
            'essensiaShare' => $essensiaShare,
            'partnerShare' => $partnerShare,
        ];
    }
}
