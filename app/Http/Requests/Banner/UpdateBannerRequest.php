<?php

namespace App\Http\Requests\Banner;

use App\Enums\BannerStatusEnum;
use App\Models\Banner;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends FormRequest
{
    protected Banner $banner;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): Response
    {
        $this->banner = Banner::findOrFail($this->route('id'));
        return Gate::authorize('update', $this->banner);
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
                'nullable',
                'string',
            ],
            'description' => [
                'bail',
                'nullable',
                'string',
                'max:150'
            ],
            'image' => [
                'bail',
                'nullable',
                Rule::imageFile()
                    ->min('1kb')
                    ->max('2mb')
            ],
            'link' => [
                'bail',
                'nullable',
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
