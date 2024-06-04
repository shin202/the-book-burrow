<?php

namespace App\Http\Requests\Cart;

use App\Models\Book;
use App\Rules\StockAvailableRule;
use App\Services\CartService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    protected Book $book;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->book = CartService::instance()->get($this->route('id'))->model;
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
            'quantity' => [
                'bail',
                'integer',
                new StockAvailableRule(Book::class, value: $this->book->id),
            ]
        ];
    }
}
