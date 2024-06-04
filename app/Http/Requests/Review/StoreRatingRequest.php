<?php

namespace App\Http\Requests\Review;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRatingRequest extends FormRequest
{
    private $book;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->book = Book::query()->where('slug', $this->route('slug'))->firstOrFail(['id']);
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
            'rating' => [
                'bail',
                'required',
                'integer',
                'between:1,5',
                Rule::unique(Rating::class, 'user_id')
                    ->where('book_id', $this->book->id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'rating.unique' => 'You have already rated this book.'
        ];
    }
}
