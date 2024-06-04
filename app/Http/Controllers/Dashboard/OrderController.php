<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderShowResource;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, OrderService $orderService, UserService $userService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $orders = $orderService->dashboardList($search, $perPage);
        $users = $userService->customerList();

        return Inertia::render('Dashboard/Order/Index', [
            'orders' => OrderCollection::make($orders),
            'users' => $users,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $orderNumber, OrderService $orderService)
    {
        $order = $orderService->show($orderNumber);

        return Inertia::render('Dashboard/Order/Show', [
            'order' => OrderShowResource::make($order),
            'can' => [
                'updateOrder' => $request->user()->can('updateStatus', $order)
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, string $orderNumber, OrderService $orderService)
    {
        $orderService->updateStatus($orderNumber, $request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Order updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
