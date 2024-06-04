<?php

namespace App\Http\Requests\Order;

use App\Models\Coupon;
use App\Services\CartService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Session::has('coupons')) {
            return true;
        }

        /** @var Coupon $coupon */
        $coupon = Session::get('coupons')['model'];
        $coupon = Coupon::query()->whereCode($coupon->code)->firstOrFail();

        $response = Gate::inspect('apply', $coupon);

        if ($response->denied()) {
            CartService::instance()->clearDiscount(Session::pull('coupons')['cartItemId']);
            throw ValidationException::withMessages([
                'code' => $response->message(),
            ]);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'bail',
                'required',
                'string',
            ],
            'last_name' => [
                'bail',
                'required',
                'string',
            ],
            'billing_email' => [
                'bail',
                'required',
                'email',
            ],
            'billing_phone' => [
                'bail',
                'required',
                'string',
                'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/'
            ],
            'billing_address' => [
                'bail',
                'required',
                'string',
            ],
            'billing_city' => [
                'bail',
                'required',
                'string',
            ],
            'billing_state' => [
                'bail',
                'required',
                'string',
            ],
            'billing_zip' => [
                'bail',
                'required',
                'string',
            ],
            'billing_country' => [
                'bail',
                'required',
                'string',
            ],
            'notes' => [
                'bail',
                'nullable',
                'string',
            ],
        ];
    }
}
