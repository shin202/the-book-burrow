<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $order = Order::query()->whereOrderNumberEquals($this->route('orderNumber'))->firstOrFail();
        return Gate::authorize('updateStatus', $order);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'bail',
                'required',
                Rule::in(OrderStatusEnum::values()),
            ]
        ];
    }
}
