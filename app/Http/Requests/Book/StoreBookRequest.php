<?php

namespace App\Http\Requests\Book;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Book::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'bail',
                'required',
                'string',
                Rule::unique(Book::class)
            ],
            'isbn' => [
                'bail',
                'required',
                'string',
                'min:10',
                'max:13',
                Rule::unique(Book::class)
            ],
            'description' => [
                'bail',
                'nullable',
                'string'
            ],
            'cover_image' => [
                'bail',
                'required',
                File::image()
                    ->min('1kb')
                    ->max('2mb')
            ],
            'number_of_pages' => [
                'bail',
                'required',
                'integer',
                'min:1'
            ],
            'publisher_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Publisher::class, 'id')
            ],
            'publication_date' => [
                'bail',
                'required',
                'date',
                'before:today'
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                Rule::unique(Book::class)
            ],
            'authors' => [
                'bail',
                'required',
                'array',
                'min:1',
            ],
            'authors.*' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Author::class, 'id')
            ],
            'genres' => [
                'bail',
                'required',
                'array',
                'min:1',
            ],
            'genres.*' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Genre::class, 'id')
            ],
            'cost' => [
                'bail',
                'required',
                'numeric',
            ],
            'price' => [
                'bail',
                'required',
                'numeric',
            ],
            'quantity_in_stock' => [
                'bail',
                'required',
                'integer',
                'min:1'
            ],
        ];
    }
}
