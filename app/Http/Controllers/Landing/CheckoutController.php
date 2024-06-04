<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Resources\Coupon\CouponResource;
use App\Services\CheckoutService;
use App\Services\CouponService;
use Exception;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CheckoutController extends Controller
{
    public function index(CouponService $couponService)
    {
        $availableCoupons = $couponService->availables();

        return Inertia::render('Landing/Checkout/Index', [
            'availableCoupons' => CouponResource::collection($availableCoupons),
            'intent' => auth()->user()->createSetupIntent(),
        ]);
    }

    public function process(CheckoutRequest $request, CheckoutService $checkoutService)
    {
        try {
            $order = $checkoutService->process($request->user(), $request->validated());
        } catch (IncompletePayment|Exception $e) {
            if ($e instanceof IncompletePayment) {
                return redirect()->route('cashier.payment', [
                    $e->payment->id,
                    'redirect' => route('landing.index')
                ]);
            }

            return redirect()->back()
                ->withErrors([
                    'message' => $e->getMessage()
                ]);
        }

        Session::flash('completed-order', $order->order_number);

        return redirect()->route('checkout.success');
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
