<?php

namespace App\Http\Requests\Publisher;

use App\Models\Publisher;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdatePublisherRequest extends FormRequest
{
    protected Publisher $publisher;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->publisher = Publisher::where('slug', $this->route('slug'))->firstOrFail();
        return Gate::authorize('update', $this->publisher);
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
                Rule::unique(Publisher::class)->ignore($this->publisher)
            ],
            'description' => [
                'bail',
                'nullable',
                'string',
            ],
            'email' => [
                'bail',
                'nullable',
                'email',
            ],
            'website' => [
                'bail',
                'nullable',
                'url',
            ],
            'phone' => [
                'bail',
                'nullable',
                'string',
            ],
            'slug' => [
                'bail',
                'nullable',
                'string',
                Rule::unique(Publisher::class)->ignore($this->publisher)
            ],
        ];
    }
}
