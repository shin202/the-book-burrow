<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateAuthUserAvatarRequest extends FormRequest
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
            'avatar' => [
                'bail',
                'required',
                File::image()
                    ->min('1kb')
                    ->max('2mb')
            ]
        ];
    }

    public function fulfill(): void
    {
        $path = $this->file('avatar')->store('uploads/avatars/' . $this->user()->id);
        $this->user()->updateAvatar($path);
    }
}
