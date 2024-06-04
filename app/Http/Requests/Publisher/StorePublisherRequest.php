<?php

namespace App\Http\Requests\Publisher;

use App\Models\Publisher;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StorePublisherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Publisher::class);
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
                'string',
                'required',
                Rule::unique(Publisher::class)
            ],
            'description' => [
                'bail',
                'string',
                'required'
            ],
            'email' => [
                'bail',
                'email',
                'required',
            ],
            'website' => [
                'bail',
                'url',
                'required'
            ],
            'phone' => [
                'bail',
                'string',
                'required'
            ],
            'slug' => [
                'bail',
                'string',
                'required',
                Rule::unique(Publisher::class)
            ],
        ];
    }
}
