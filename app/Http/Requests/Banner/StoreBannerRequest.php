<?php

namespace App\Http\Requests\Banner;

use App\Enums\BannerStatusEnum;
use App\Models\Banner;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        return Gate::authorize('create', Banner::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'bail',
                'required',
                'string',
            ],
            'description' => [
                'bail',
                'required',
                'string',
                'max:150',
            ],
            'image' => [
                'bail',
                'required',
                Rule::imageFile()
                    ->min('1kb')
                    ->max('2mb')
            ],
            'link' => [
                'bail',
                'required',
                'string',
            ],
            'status' => [
                'bail',
                'nullable',
                Rule::enum(BannerStatusEnum::class)
            ]
        ];
    }
}
