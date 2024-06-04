<?php

namespace App\Http\Requests\Deal;

use App\Enums\DiscountTypeEnum;
use App\Models\Book;
use App\Models\DailyDeal;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateDealRequest extends FormRequest
{
    protected $deal;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->deal = DailyDeal::query()->findOrFail($this->route('id'));
        return Gate::authorize('update', $this->deal);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookPrice = Book::query()->find($this->input('book_id'), ['price'])?->price;
        $maxDiscountValue = $this->input('discount_type') === DiscountTypeEnum::PERCENTAGE->value
            ? 100
            : $bookPrice;

        return [
            'book_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Book::class, 'id')
            ],
            'discount_type' => [
                'bail',
                'nullable',
                'string',
                Rule::enum(DiscountTypeEnum::class)
            ],
            'discount_value' => [
                'bail',
                'nullable',
                'numeric',
                "max:$maxDiscountValue",
            ],
            'start_date' => [
                'bail',
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'bail',
                'nullable',
                'date',
                'after:start_date'
            ],
        ];
    }
}
