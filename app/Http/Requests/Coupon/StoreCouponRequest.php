<?php

namespace App\Http\Requests\Coupon;

use App\Enums\DiscountTypeEnum;
use App\Models\Author;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Coupon::class);
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
                Rule::unique(Coupon::class)
            ],
            'type' => [
                'bail',
                'required',
                'string',
                Rule::enum(DiscountTypeEnum::class)
            ],
            'value' => [
                'bail',
                'required',
                'numeric',
                'min:0'
            ],
            'description' => [
                'bail',
                'nullable',
                'string'
            ],
            'minimum_order_amount' => [
                'bail',
                'nullable',
                'numeric',
            ],
            'usage_limit' => [
                'bail',
                'nullable',
                'integer',
            ],
            'usage_per_user' => [
                'bail',
                'nullable',
                'integer',
            ],
            'valid_from' => [
                'bail',
                'required',
                'date',
                'after_or_equal:today'
            ],
            'valid_to' => [
                'bail',
                'required',
                'date',
                'after:valid_from'
            ],
            'is_active' => [
                'bail',
                'nullable',
                'boolean'
            ],
            'couponable_type' => [
                'bail',
                'required',
                'string',
                Rule::in(['user', 'book', 'author', 'genre'])
            ],
            'couponable_id' => [
                'bail',
                'required',
                'numeric',
                Rule::when($this->input('assign_for') === 'users',
                    Rule::exists(User::class, 'id')
                ),
                Rule::when($this->input('assign_for') === 'books',
                    Rule::exists(Book::class, 'id')
                ),
                Rule::when($this->input('assign_for') === 'authors',
                    Rule::exists(Author::class, 'id')
                ),
                Rule::when($this->input('assign_for') === 'genres',
                    Rule::exists(Genre::class, 'id')
                )
            ]
        ];
    }
}
