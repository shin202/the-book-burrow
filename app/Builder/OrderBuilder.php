<?php

namespace App\Builder;

use App\Traits\TimeUnitToSqlFormat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderBuilder extends Builder
{
    use TimeUnitToSqlFormat;

    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereOrderNumberEquals(string $orderNumber = null): self
    {
        return $this->when($orderNumber, function ($query) use ($orderNumber) {
            return $query->where('order_number', $orderNumber);
        });
    }

    public function whereTotalGreaterThanOrEquals($value)
    {
        return $this->where('billing_total', '>=', $value);
    }

    public function whereCreatedAtEquals($date)
    {
        return $this->whereDate('created_at', Carbon::parse($date));
    }

    public function whereStatus(string $status)
    {
        return $this->whereHas('currentStatus', function ($query) use ($status) {
            $query->where('status', $status);
        });
    }

    public function totalSales()
    {
        return $this->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('SUM(order_items.quantity) as total_sales')
            ->first()
            ->total_sales;
    }

    public function salesBy(string $unit = 'months', array $range = [])
    {
        $params = collect($range)->map(fn() => '?')->implode(',');

        $timeRangeQuery =
            "DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') IN ($params)";

        return $this->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereRaw($timeRangeQuery, $range)
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') as time")
            ->selectRaw('SUM(order_items.quantity) as total_sales')
            ->groupBy('time')
            ->orderBy('time');
    }

    public function totalRevenue()
    {
        return $this->sum('billing_total');
    }

    public function revenueBy(string $unit = 'months', array $range = [])
    {
        $params = collect($range)->map(fn() => '?')->implode(',');
        $query =
            "DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') IN ($params)";

        return $this
            ->whereRaw($query, $range)
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') as time")
            ->selectRaw('SUM(billing_total) as total_revenue')
            ->groupBy('time')
            ->orderBy('time')
            ->get();
    }

    public function totalOrders()
    {
        return $this->count();
    }

    public function ordersBy(string $unit = 'months', array $range = [])
    {
        $params = collect($range)->map(fn() => '?')->implode(',');
        $query = "DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') IN ($params)";

        return $this
            ->whereRaw($query, $range)
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$this->toSqlFormat($unit)}') as time")
            ->selectRaw('COUNT(*) as total_orders')
            ->groupBy('time')
            ->orderBy('time')
            ->get();
    }

    public function salesByGenre(string $unit = null)
    {
        $method = 'sub' . ucfirst($unit);

        return $this
            ->when(isset($unit), function ($query) use ($method) {
                return $query->whereBetween('order_items.created_at', [now()->$method(), now()]);
            })
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('book_genre', 'order_items.book_id', '=', 'book_genre.book_id')
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
            ->addSelect([
                DB::raw('SUM(order_items.quantity) as total_sales'),
                DB::raw('genres.name as genre'),
            ])
            ->orderByDesc('total_sales')
            ->groupBy('genre');
    }

    public function earningsBy(string $unit = 'months', array $range = [])
    {
        $dateFormat = $this->toSqlFormat($unit);

        return $this
            ->whereIn(DB::raw("DATE_FORMAT(orders.created_at, '{$dateFormat}')"), $range)
            ->addSelect([
                DB::raw("DATE_FORMAT(orders.created_at, '{$dateFormat}') as time"),
                DB::raw('SUM(billing_total) as total_revenue'),
                DB::raw('SUM(total_profit) as total_profit')
            ])
            ->orderBy('time')
            ->groupBy('time');
    }
}
