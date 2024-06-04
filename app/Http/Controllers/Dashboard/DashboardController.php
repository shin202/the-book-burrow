<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request, ReportService $reportService)
    {
        $unit = $request->input('reportUnit', 'months');
        $limit = $request->input('reportLimit', 3);

        $sales = $reportService->salesReportBy($unit, $limit);
        $revenue = $reportService->revenueReportBy($unit, $limit);
        $orders = $reportService->ordersReportBy($unit, $limit);
        $customers = $reportService->customersReportBy($unit, $limit);
        $recentOrders = $reportService->recentOrdersReport();
        $books = $reportService->bestSellerBy($request->input('bestSellerBy'));
        $salesByGenre = $reportService->salesByGenre();
        $earnings = $reportService->earningsBy();

        return Inertia::render('Dashboard/Index', [
            'sales' => fn() => $sales,
            'revenue' => fn() => $revenue,
            'orders' => fn() => $orders,
            'customers' => fn() => $customers,
            'recentOrders' => $recentOrders,
            'bestSellers' => $books,
            'salesByGenre' => $salesByGenre,
            'earnings' => $earnings,
        ]);
    }

    public function download(Request $request)
    {
        if (Storage::exists($request->filePath)) {
            return Storage::download($request->filePath);
        }

        abort(404);
    }
}
