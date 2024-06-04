<?php

namespace App\Http\Requests\Auth;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResendEmailVerificationRequest extends FormRequest
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
            //
        ];
    }

    public function fulfill(): void
    {
        $emailVerifyToken = $this->user()->createEmailVerifyToken();
        $this->user()->notify(new VerifyEmailNotification($emailVerifyToken->token));
    }
}
