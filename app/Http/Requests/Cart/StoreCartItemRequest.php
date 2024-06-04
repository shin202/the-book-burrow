<?php

namespace App\Http\Requests\Cart;

use App\Models\Book;
use App\Rules\StockAvailableRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'product_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Book::class, 'id')
            ],
            'quantity' => [
                'bail',
                'required',
                'integer',
                new StockAvailableRule(Book::class, value: $this->input('product_id')),
            ]
        ];
    }
}
