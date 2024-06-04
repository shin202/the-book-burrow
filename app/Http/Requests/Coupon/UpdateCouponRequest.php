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

class UpdateCouponRequest extends FormRequest
{
    protected Coupon $coupon;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->coupon = Coupon::whereCode($this->route('code'))->firstOrFail();
        return Gate::authorize('update', $this->coupon);
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
                'nullable',
                'string',
                Rule::unique(Coupon::class)->ignore($this->coupon)
            ],
            'type' => [
                'bail',
                'nullable',
                'string',
                Rule::enum(DiscountTypeEnum::class)
            ],
            'value' => [
                'bail',
                'nullable',
                'numeric',
                'min:0'
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
                'nullable',
                'date',
                'after_or_equal:today'
            ],
            'valid_to' => [
                'bail',
                'nullable',
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
                Rule::in(['users', 'books', 'authors', 'genres'])
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
