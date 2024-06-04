<?php

namespace App\Http\Requests\Publisher;

use App\Models\Publisher;
use App\Traits\FileImportValidateTrait;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImportPublisherRequest extends FormRequest
{
    use FileImportValidateTrait;

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
        return $this->fileValidateRules();
    }

    public function fulfill(): false|string
    {
        return $this->file('file')->store('private/imports');
    }
}
