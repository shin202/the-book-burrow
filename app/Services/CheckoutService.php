<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CheckoutService
{
    public function __construct(protected OrderService $orderService)
    {

    }

    /**
     * @throws IncompletePayment
     */
    public function process(User $user, array $data)
    {
        DB::beginTransaction();
        
        $data['payment_method'] = PaymentMethodEnum::STRIPE;
        $order = $this->orderService->store($user, $data);

        try {
            $user->charge(round(CartService::instance()->total(), 2) * 100, $data['stripe_payment_method_id'], [
                'return_url' => route('checkout.success'),
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);
        } catch (Exception|IncompletePayment $e) {
            if ($e instanceof IncompletePayment) {
                throw $e;
            }

            DB::rollBack();
            throw $e;
        }

        DB::commit();

        CartService::instance()->destroy();

        return $order;
    }
}
