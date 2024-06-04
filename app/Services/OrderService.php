<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Notifications\SendOrderStatusUpdatedNotification;
use App\Traits\Reportable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{
    use Reportable;

    public function __construct(protected Order $order, protected Book $book)
    {

    }

    public function dashboardList(string $search = null, int $perPage = 5)
    {
        return QueryBuilder::for(Order::class)->with(
            [
                'user' => fn($query) => $query->select('id', 'username'),
                'currentStatus' => fn($query) => $query->select('order_statuses.order_id', 'status')
            ]
        )
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('order_number'),
                AllowedFilter::scope('user', 'whereUser'),
                AllowedFilter::scope('total', 'whereTotalGreaterThanOrEquals'),
                AllowedFilter::exact('user_id')
            ])
            ->defaultSort('-created_at')
            ->whereOrderNumberEquals($search)
            ->paginate($perPage, ['id', 'order_number', 'user_id', 'billing_total', 'total_profit', 'created_at', 'user_id']);
    }

    public function user(User $user, int $perPage = 10)
    {
        return QueryBuilder::for(Order::class)
            ->where('user_id', $user->id)
            ->with(
                [
                    'currentStatus' => fn($query) => $query->select('order_statuses.order_id', 'status'),
                ]
            )
            ->allowedFilters([
                AllowedFilter::exact('order_number'),
                AllowedFilter::scope('order_date', 'whereCreatedAtEquals'),
                AllowedFilter::scope('status', 'whereStatus')
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage, ['id', 'order_number', 'billing_total', 'created_at']);
    }

    public function findByOrderNumber(string $orderNumber)
    {
        return $this->order
            ->newQuery()
            ->whereOrderNumberEquals($orderNumber)->firstOrFail();
    }

    public function show(string $orderNumber)
    {
        return $this->order
            ->with(
                [
                    'user' => fn($query) => $query->select('id', 'username'),
                    'items',
                    'payment',
                    'statuses' => fn($query) => $query->select('order_id', 'status', 'created_at'),
                ]
            )
            ->where('order_number', $orderNumber)->firstOrFail();
    }

    public function store(User $user, array $data)
    {
        $orderItems = CartService::instance()->items()->mapWithKeys(function ($item) {
            /** @var Book $model */
            $quantity = $item->quantity;

            $book = $this->book->newQuery()->lockForUpdate()->find($item->productId);

            if ($book->quantity_in_stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'There is not enough stock for ' . $item->model->title . '.'
                ]);
            }

            $book->decrement('quantity_in_stock', $quantity);

            $orderItem[$book->id] = [
                'quantity' => $quantity,
                'discount' => $item->discountTotal,
                'price' => $item->price,
                'options' => $item->attributes
            ];

            return $orderItem;
        });

        $totalProfit = CartService::instance()->items()
            ->sum(fn($item) => $item->model->profit * $item->quantity);

        $coupon = Session::pull('coupons');
        $couponCode = null;

        if (!is_null($coupon)) {
            $couponCode = $coupon['model']->code;
            $coupon['model']->increment('used_count');
            $user->coupons()->attach($coupon['model']);
        }

        $order = $user->orders()->create([
            'order_number' => $this->generateOrderNumber(),
            'billing_name' => $data['first_name'] . ' ' . $data['last_name'],
            'billing_email' => $data['billing_email'],
            'billing_phone' => $data['billing_phone'],
            'billing_address' => $data['billing_address'],
            'billing_city' => $data['billing_city'],
            'billing_state' => $data['billing_state'],
            'billing_zip' => $data['billing_zip'],
            'billing_country' => $data['billing_country'],
            'billing_discount_code' => $couponCode,
            'billing_discount' => round(CartService::instance()->discount(), 2),
            'billing_subtotal' => round(CartService::instance()->subtotal(), 2),
            'billing_total' => round(CartService::instance()->total(), 2),
            'total_profit' => $totalProfit,
            'payment_method' => $data['payment_method'],
        ]);

        $order->items()->attach($orderItems);

        $order->statuses()->create([
            'status' => OrderStatusEnum::PENDING,
        ]);

        return $order;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . substr(date('y'), -2) . date('m') . date('d') . Str::upper(Str::random(8));
    }

    public function updateStatus(string $orderNumber, array $data): void
    {
        $order = $this->order->where('order_number', $orderNumber)->firstOrFail();
        $order->statuses()->create($data);

        $order->user->notify(new SendOrderStatusUpdatedNotification($order, $data['status']));
    }

    public function cancel(string $oderNumber)
    {
        $order = $this->order->newQuery()
            ->whereOrderNumberEquals($oderNumber)
            ->firstOrFail();

        $order->statuses()->create([
            'status' => OrderStatusEnum::CANCELLED,
        ]);

        return $order;
    }
}
