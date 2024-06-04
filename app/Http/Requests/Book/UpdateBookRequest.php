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

class UpdateBookRequest extends FormRequest
{
    protected Book $book;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->book = Book::where('slug', $this->route('slug'))->firstOrFail();
        return Gate::authorize('update', $this->book);
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
                'nullable',
                'string',
                Rule::unique(Book::class)->ignore($this->book),
            ],
            'isbn' => [
                'bail',
                'nullable',
                'string',
                Rule::unique(Book::class)->ignore($this->book),
            ],
            'description' => [
                'bail',
                'nullable',
                'string',
            ],
            'number_of_pages' => [
                'bail',
                'nullable',
                'integer',
            ],
            'publisher_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists(Publisher::class, 'id'),
            ],
            'publication_date' => [
                'bail',
                'nullable',
                'date',
                'before:today',
            ],
            'slug' => [
                'bail',
                'nullable',
                'string',
                Rule::unique(Book::class)->ignore($this->book),
            ],
            'cover_image' => [
                'bail',
                'nullable',
                Rule::imageFile()
                    ->min('100kb')
                    ->max('2mb')
            ],
            'authors' => [
                'bail',
                'nullable',
                'array',
                'min:1',
            ],
            'authors.*' => [
                'bail',
                'integer',
                Rule::exists(Author::class, 'id'),
            ],
            'genres' => [
                'bail',
                'nullable',
                'array',
                'min:1',
            ],
            'genres.*' => [
                'bail',
                'integer',
                Rule::exists(Genre::class, 'id'),
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
