<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->store($request->user(), $request->validated());

        CartService::instance()->destroy();

        Session::flash('completed-order', $order->order_number);

        return redirect()->route('order.success');
    }

    public function success()
    {
        if (!Session::has('completed-order')) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('Landing/Order/Success', [
            'order_number' => Session::get('completed-order'),
        ]);
    }
}
