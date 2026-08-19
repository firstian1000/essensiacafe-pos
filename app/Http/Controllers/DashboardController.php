<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
        [$date, $startDate, $endDate, $periodType, $periodWeek, $periodMonth, $periodYear, $periodLabel] = $this->resolvePeriod($request);
        $paymentFilter = $request->get('payment_filter', 'all');
        $brandFilter = $request->get('brand_filter', 'all');

        $data = $this->dashboardService->getDashboardData($date, $paymentFilter, $brandFilter, $startDate, $endDate);
        $data['selectedDate'] = $date;
        $data['paymentFilter'] = $paymentFilter;
        $data['brandFilter'] = $brandFilter;
        $data['periodType'] = $periodType;
        $data['periodWeek'] = $periodWeek;
        $data['periodMonth'] = $periodMonth;
        $data['periodYear'] = $periodYear;
        $data['periodLabel'] = $periodLabel;
        $data['periodStart'] = $startDate->format('Y-m-d');
        $data['periodEnd'] = $endDate->format('Y-m-d');

        return view('dashboard.index', $data);
    }


    public function export(Request $request)
    {
        [$date, $startDate, $endDate, $periodType, $periodWeek, $periodMonth, $periodYear, $periodLabel] = $this->resolvePeriod($request);
        $paymentFilter = $request->get('payment_filter', 'all');
        $brandFilter = $request->get('brand_filter', 'all');
        $format = $request->get('format', 'excel');

        $exportData = $this->buildExportData($date, $paymentFilter, $brandFilter, $startDate, $endDate, $periodLabel);

        if ($format === 'pdf') {
            $filename = 'laporan-esensia-koffie-' . $periodType . '-' . $date . '.pdf';

            return Pdf::loadView('dashboard.export-pdf', $exportData)
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        $filename = 'rekap-esensia-koffie-' . $periodType . '-' . $date . '.xls';

        return response()->streamDownload(function () use ($exportData) {
            echo view('dashboard.export', $exportData)->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function buildExportData(string $date, string $paymentFilter, string $brandFilter, Carbon $startDate, Carbon $endDate, string $periodLabel): array
    {
        $data = $this->dashboardService->getDashboardData($date, $paymentFilter, $brandFilter, $startDate, $endDate);
        $ordersQuery = Order::with(['table', 'items.menu.category'])
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
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
            'periodLabel' => $periodLabel,
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

    private function resolvePeriod(Request $request): array
    {
        $periodType = in_array($request->get('period_type'), ['day', 'week', 'month', 'year'], true)
            ? $request->get('period_type')
            : 'day';
        $date = Carbon::parse($request->get('date', now()->format('Y-m-d')));
        $periodYear = (int) $request->get('period_year', $date->year);
        $periodMonth = (int) $request->get('period_month', $date->month);
        $periodMonth = max(1, min(12, $periodMonth));
        $periodWeek = (int) $request->get('period_week', 1);
        $periodWeek = max(1, min(4, $periodWeek));

        if ($periodType === 'week') {
            $startDay = (($periodWeek - 1) * 7) + 1;
            $monthDate = Carbon::create($periodYear, $periodMonth, 1);
            $startDate = $monthDate->copy()->day($startDay);
            $endDate = $periodWeek === 4
                ? $monthDate->copy()->endOfMonth()
                : $monthDate->copy()->day($startDay + 6);
            $selectedDate = $startDate->format('Y-m-d');
            $label = 'Minggu ' . $periodWeek . ' ' . $monthDate->translatedFormat('F Y');
        } elseif ($periodType === 'month') {
            $startDate = Carbon::create($periodYear, $periodMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $selectedDate = $startDate->format('Y-m-d');
            $label = $startDate->translatedFormat('F Y');
        } elseif ($periodType === 'year') {
            $startDate = Carbon::create($periodYear, 1, 1)->startOfYear();
            $endDate = $startDate->copy()->endOfYear();
            $selectedDate = $startDate->format('Y-m-d');
            $label = (string) $periodYear;
        } else {
            $startDate = $date->copy();
            $endDate = $date->copy();
            $selectedDate = $date->format('Y-m-d');
            $label = $date->translatedFormat('d F Y');
        }

        return [$selectedDate, $startDate, $endDate, $periodType, $periodWeek, $periodMonth, $periodYear, $label];
    }
}
