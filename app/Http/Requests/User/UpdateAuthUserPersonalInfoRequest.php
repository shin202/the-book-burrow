<?php

namespace App\Http\Requests\User;

use App\Enums\UserGenderEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuthUserPersonalInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'first_name' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'last_name' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'date_of_birth' => [
                'bail',
                'date',
                'before:today',
            ],
            'gender' => [
                'bail',
                'nullable',
                Rule::enum(UserGenderEnum::class),
            ],
            'country' => [
                'bail',
                'string',
                'regex:/^[a-zA-Z\s]+$/',
            ],
        ];
    }

    public function fulfill(): void
    {
        $this->user()->upsertPersonalInformation($this->validated());
    }
}
