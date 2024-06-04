<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Traits\Reportable;
use Illuminate\Support\Number;

class ReportService
{
    use Reportable;

    public function __construct(protected Order $order, protected User $user, protected Book $book)
    {

    }

    public function salesReportBy(string $unit = 'months', int $limit = 3): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $totalSales = $this->order
            ->newQuery()
            ->totalSales();
        $sales = $this->order
            ->newQuery()
            ->salesBy($unit, $timeRange->toArray())
            ->get('*');


        $method = 'sub' . ucfirst($unit);
        $currentSales = $sales->firstWhere('time', now()->format($this->getTimeFormat($unit)));
        $lastSales = $sales->firstWhere('time', now()->$method()->format($this->getTimeFormat($unit)));

        $labels = $this->createLabelsFor($unit, $limit);
        $datasets = $this->createDatasetsFor($sales, 'total_sales', $unit, $limit);
        [$rate, $status] = $this->calcChangeRate($currentSales?->total_sales, $lastSales?->total_sales);

        return $this->createReportData(
            'Sales By ' . ucfirst($unit),
            [
                'abbr' => Number::abbreviate($totalSales, 2, 2),
                'full' => Number::format($totalSales, 2, 2),
            ],
            $currentSales?->total_sales ?? 0,
            $lastSales?->total_sales ?? 0,
            $rate,
            $status,
            $labels,
            $datasets
        );
    }

    public function revenueReportBy(string $unit = 'months', int $limit = 3): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $totalRevenue = $this->order
            ->newQuery()
            ->totalRevenue();

        $revenue = $this->order
            ->newQuery()
            ->revenueBy($unit, $timeRange->toArray());

        $method = 'sub' . ucfirst($unit);
        $currentRevenue = $revenue->firstWhere('time', now()->format($this->getTimeFormat($unit)));
        $lastRevenue = $revenue->firstWhere('time', now()->$method()->format($this->getTimeFormat($unit)));

        $labels = $this->createLabelsFor($unit, $limit);
        $datasets = $this->createDatasetsFor($revenue, 'total_revenue', $unit, $limit);
        [$rate, $status] = $this->calcChangeRate($currentRevenue?->total_revenue, $lastRevenue?->total_revenue);

        return $this->createReportData(
            'Revenue By ' . ucfirst($unit),
            [
                'abbr' => Number::abbreviate($totalRevenue, 2, 2),
                'full' => Number::currency($totalRevenue),
            ],
            $currentRevenue?->total_revenue ?? 0,
            $lastRevenue?->total_revenue ?? 0,
            $rate,
            $status,
            $labels,
            $datasets
        );
    }

    public function ordersReportBy(string $unit = 'months', int $limit = 3): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $totalOrders = $this->order
            ->newQuery()
            ->totalOrders();

        $orders = $this->order
            ->newQuery()
            ->ordersBy($unit, $timeRange->toArray());

        $method = 'sub' . ucfirst($unit);
        $currentOrders = $orders->firstWhere('time', now()->format($this->getTimeFormat($unit)));
        $lastOrders = $orders->firstWhere('time', now()->$method()->format($this->getTimeFormat($unit)));

        $labels = $this->createLabelsFor($unit, $limit);
        $datasets = $this->createDatasetsFor($orders, 'total_orders', $unit, $limit);
        [$rate, $status] = $this->calcChangeRate($currentOrders?->total_orders, $lastOrders?->total_orders);

        return $this->createReportData(
            'Orders By ' . ucfirst($unit),
            [
                'abbr' => Number::abbreviate($totalOrders, 2, 2),
                'full' => Number::format($totalOrders, 2, 2),
            ],
            $currentOrders?->total_orders ?? 0,
            $lastOrders?->total_orders ?? 0,
            $rate,
            $status,
            $labels,
            $datasets
        );
    }

    public function customersReportBy(string $unit = 'months', int $limit = 3): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $totalCustomers = $this->user
            ->newQuery()
            ->customers()
            ->count();

        $customers = $this->user
            ->newQuery()
            ->customersBy($unit, $timeRange->toArray());

        $method = 'sub' . ucfirst($unit);
        $currentCustomers = $customers->firstWhere('time', now()->format($this->getTimeFormat($unit)));
        $lastCustomers = $customers->firstWhere('time', now()->$method()->format($this->getTimeFormat($unit)));

        $labels = $this->createLabelsFor($unit, $limit);
        $datasets = $this->createDatasetsFor($customers, 'total_customers', $unit, $limit);
        [$rate, $status] = $this->calcChangeRate($currentCustomers?->total_customers, $lastCustomers?->total_customers);

        return $this->createReportData(
            'Customers By ' . ucfirst($unit),
            [
                'abbr' => Number::abbreviate($totalCustomers, 2, 2),
                'full' => Number::format($totalCustomers, 2, 2),
            ],
            $currentCustomers?->total_customers ?? 0,
            $lastCustomers?->total_customers ?? 0,
            $rate,
            $status,
            $labels,
            $datasets
        );
    }

    public function recentOrdersReport(string $unit = 'days', int $limit = 5): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $recentOrders = $this->order->newQuery()
            ->ordersBy($unit, $timeRange->toArray());

        $dataLabels = $this->createLabelsFor($unit, $limit);
        $datasets = $this->createDatasetsFor($recentOrders, 'total_orders', $unit, $limit);

        return [
            'chart' => $this->toChartData('Recent Orders', $dataLabels, $datasets),
        ];
    }

    public function bestSellerBy(string $unit = null, int $limit = 5)
    {
        return $this->book->newQuery()
            ->bestSellerBy($unit)
            ->addSelect(['books.id', 'title', 'slug', 'cover_image', 'quantity_in_stock'])
            ->limit($limit)
            ->get()
            ->map(fn($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'cover_image' => $book->cover_image,
                'slug' => $book->slug,
                'total_sold' => $book->total_sold,
                'total_revenue' => [
                    'abbr' => Number::abbreviate($book->total_revenue, 2, 2),
                    'full' => Number::currency($book->total_revenue),
                ],
                'stock_status' => $book->stock_status,
            ]);
    }

    public function salesByGenre(string $unit = null, int $limit = 5): array
    {
        $sales = $this->order->newQuery()
            ->salesByGenre($unit)
            ->limit($limit)
            ->get();

        $dataLabels = $sales->pluck('genre')->toArray();
        $datasets = $sales->pluck('total_sales')->toArray();

        return [
            'chart' => $this->toChartData('Sales By Genre', $dataLabels, $datasets),
        ];
    }

    public function earningsBy(string $unit = 'months', int $limit = 3): array
    {
        $timeRange = $this->getTimeRange($unit, $limit);

        $earnings = $this->order
            ->newQuery()
            ->earningsBy($unit, $timeRange->toArray())
            ->get();

        $labels = $this->createLabelsFor($unit, $limit);
        $revenueDatasets = $this->createDatasetsFor($earnings, 'total_revenue', $unit, $limit);
        $profitDatasets = $this->createDatasetsFor($earnings, 'total_profit', $unit, $limit);

        return [
            'total_revenue' => [
                'abbr' => Number::abbreviate($earnings->sum('total_revenue'), 2, 2),
                'full' => Number::currency($earnings->sum('total_revenue')),
            ],
            'total_profit' => [
                'abbr' => Number::abbreviate($earnings->sum('total_profit'), 2, 2),
                'full' => Number::currency($earnings->sum('total_profit')),
            ],
            'chart' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Revenue',
                        'data' => $revenueDatasets,
                    ],
                    [
                        'label' => 'Profit',
                        'data' => $profitDatasets,
                    ]
                ]
            ]
        ];
    }
}
