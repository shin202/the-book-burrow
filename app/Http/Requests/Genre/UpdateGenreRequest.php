<?php

namespace App\Http\Requests\Genre;

use App\Models\Genre;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
{
    protected Genre $genre;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->genre = Genre::whereSlug($this->route('slug'))->firstOrFail();
        return Gate::authorize('update', $this->genre);
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
              'nullable',
              'string',
              Rule::unique(Genre::class)->ignore($this->genre)
            ],
            'description' => [
                'bail',
                'nullable',
                'string',
            ],
            'slug' => [
                'bail',
                'nullable',
                'string',
                Rule::unique(Genre::class)->ignore($this->genre)
            ],
        ];
    }
}
