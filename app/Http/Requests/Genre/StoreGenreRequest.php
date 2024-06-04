<?php

namespace App\Http\Requests\Genre;

use App\Models\Genre;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreGenreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): \Illuminate\Auth\Access\Response
    {
        return Gate::authorize('create', Genre::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                Rule::unique(Genre::class)
            ],
            'description' => [
                'bail',
                'required',
                'string',
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Genre::class)
            ]
        ];
    }
}
