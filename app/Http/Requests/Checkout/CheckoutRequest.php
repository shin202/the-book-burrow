<?php

namespace App\Http\Requests\Checkout;

use App\Http\Requests\Order\StoreOrderRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckoutRequest extends StoreOrderRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'stripe_payment_method_id' => [
                'bail',
                'required',
                'string',
            ],
        ];
    }
}
