<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangeAuthUserPasswordRequest extends FormRequest
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
            'current_password' => [
                'bail',
                'required',
                'string',
            ],
            'new_password' => [
                'bail',
                'required',
                'string',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*.,])[A-Za-z0-9!@#$%^&*.,]{8,}$/',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.regex' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }

    public function fulfill(): RedirectResponse
    {
        $isPasswordValid = Hash::check($this->validated('current_password'), Auth::user()->password);

        if (!$isPasswordValid) {
            return redirect()->back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        Auth::user()->update([
            'password' => Hash::make($this->validated('new_password')),
        ]);

        return redirect()->back()->with([
            'toast.severity' => 'success',
            'toast.message' => 'Your password has been updated.',
        ]);
    }
}
