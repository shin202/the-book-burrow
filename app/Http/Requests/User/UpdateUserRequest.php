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

class UpdateUserRequest extends FormRequest
{
    protected User $user;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->user = User::where('username', $this->route('username'))->firstOrFail();
        return Gate::authorize('update', $this->user);
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
                'nullable',
                'string',
                'min:5',
                'max:20',
                Rule::unique(User::class)->ignore($this->user)
            ],
            'email' => [
                'bail',
                'nullable',
                'email',
                Rule::unique(User::class)->ignore($this->user)
            ],
            'password' => [
                'bail',
                'nullable',
                'string',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*.,])[A-Za-z0-9!@#$%^&*.,]{8,}$/',
                'confirmed',
            ],
            'role_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists(Role::class, 'id')
            ],
            'first_name' => [
                'bail',
                'nullable',
                'string',
                'min:2',
            ],
            'last_name' => [
                'bail',
                'nullable',
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
