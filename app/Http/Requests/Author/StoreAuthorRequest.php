<?php

namespace App\Http\Requests\Author;

use App\Models\Author;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Author::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'bail',
                'required',
                'string',
            ],
            'last_name' => [
                'bail',
                'required',
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
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Author::class)
            ]
        ];
    }
}
