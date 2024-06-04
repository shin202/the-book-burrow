<?php

namespace App\Http\Requests\Author;

use App\Models\Author;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAuthorRequest extends FormRequest
{
    protected Author $author;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->author = Author::where('slug', $this->route('slug'))->firstOrFail();
        return Gate::authorize('update', $this->author);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'bail',
                'nullable',
                'string',
            ],
            'last_name' => [
                'bail',
                'nullable',
                'string',
            ],
            'biography' => [
                'bail',
                'nullable',
                'string',
            ],
            'avatar' => [
                'bail',
                'nullable',
                Rule::imageFile()
                    ->min('1kb')
                    ->max('2mb')
            ],
            'slug' => [
                'bail',
                'nullable',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Author::class)->ignore($this->author)
            ]
        ];
    }
}
