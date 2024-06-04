<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait FileImportValidateTrait
{
    public function fileValidateRules(): array
    {
        return [
            'file' => [
                'bail',
                'required',
                Rule::file()
                    ->extensions(['xlsx', 'xls'])
            ]
        ];
    }
}
