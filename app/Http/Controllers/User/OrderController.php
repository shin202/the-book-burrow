<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderShowResource;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request, OrderService $orderService)
    {
        $orders = $orderService->user($request->user(), $request->input('perPage', 10));

        return Inertia::render('User/Order/Index', [
            'orders' => OrderCollection::make($orders)
        ]);
    }

    public function show(string $orderNumber, OrderService $orderService)
    {
        $order = $orderService->show($orderNumber);

        return Inertia::render('User/Order/Show', [
            'order' => OrderShowResource::make($order),
        ]);
    }

    public function invoice(string $orderNumber, InvoiceService $invoiceService)
    {
        return $invoiceService->generate($orderNumber);
    }

    public function cancel(CancelOrderRequest $request, string $orderNumber, OrderService $orderService)
    {
        $orderService->cancel($orderNumber);

        return redirect()
            ->back();
    }
}
