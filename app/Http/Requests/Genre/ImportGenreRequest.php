<?php

namespace App\Http\Requests\Genre;

use App\Models\Genre;
use App\Traits\FileImportValidateTrait;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImportGenreRequest extends FormRequest
{
    use FileImportValidateTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Genre::class);
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
}
