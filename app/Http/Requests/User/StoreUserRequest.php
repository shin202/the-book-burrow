<?php

namespace App\Http\Requests\User;

use App\Enums\UserGenderEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', User::class);
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
            'role_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(Role::class, 'id')
            ],
            'first_name' => [
                'bail',
                'required',
                'string',
                'min:2',
            ],
            'last_name' => [
                'bail',
                'required',
                'string',
                'min:2',
            ],
            'gender' => [
                'bail',
                'nullable',
                Rule::enum(UserGenderEnum::class),
            ],
            'country' => [
                'bail',
                'nullable',
                'string',
                'min:2',
            ],
            'date_of_birth' => [
                'bail',
                'nullable',
                'date',
                'before:today',
            ],
        ];
    }
}
