<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'username' => [
                'bail',
                'required',
                'string',
                'min:5',
                'max:20',
                Rule::unique(User::class)
            ],
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                Rule::unique(User::class)
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*.,])[A-Za-z0-9!@#$%^&*.,]{8,}$/',
                'confirmed',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.string' => 'Username must be a string',
            'username.min' => 'Username must be at least 3 characters',
            'username.max' => 'Username must be at most 20 characters',
            'email.required' => 'Email is required',
            'email.string' => 'Email must be a string',
            'email.email' => 'Email must be a valid email address',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.regex' => 'Password must be at least 8 characters, contain at least one uppercase letter, one lowercase letter, one number, and one special character',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
