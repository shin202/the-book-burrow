<?php

namespace App\Http\Requests\Coupon;

use App\Models\Coupon;
use App\Services\CartService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class ApplyCouponRequest extends FormRequest
{
    private Coupon|null $coupon;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Coupon|null $coupon */
        $this->coupon = Coupon::query()
            ->whereBookIdIn(CartService::instance()->items()->pluck('productId')->toArray())
            ->whereActive()
            ->whereCode($this->input('code'))
            ->first();

        if (is_null($this->coupon)) {
            throw ValidationException::withMessages([
                'code' => 'The coupons is not valid or not available for the items in your cart.'
            ]);
        }

        $response = Gate::inspect('apply', $this->coupon);

        if ($response->denied()) {
            throw ValidationException::withMessages([
                'code' => $response->message()
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
            'code' => [
                'bail',
                'required',
                'string',
            ]
        ];
    }

    public function fulfill(): ?Coupon
    {
        $cartItemId = CartService::instance()->items()->where('productId', $this->coupon->couponable_id)->first()->id;
        Session::put('coupons', [
            'cartItemId' => $cartItemId,
            'model' => $this->coupon,
        ]);

        CartService::instance()->setDiscount($cartItemId, $this->coupon->value, $this->coupon->type);

        return $this->coupon;
    }
}
