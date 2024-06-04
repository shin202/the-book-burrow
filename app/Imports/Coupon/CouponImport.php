<?php

namespace App\Imports\Coupon;

use App\Enums\DiscountTypeEnum;
use App\Models\Coupon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CouponImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private const int BATCH_SIZE = 1000;
    private const int CHUNK_SIZE = 1000;


    public function onFailure(Failure ...$failures)
    {
        // TODO: Implement onFailure() method.
    }

    public function model(array $row): Coupon
    {
        return new Coupon([
            'code' => $row['code'],
            'type' => $row['type'],
            'value' => $row['value'],
            'minimum_order_amount' => $row['minimum_order_amount'],
            'usage_limit' => $row['usage_limit'],
            'usage_per_user' => $row['usage_per_user'],
            'valid_from' => $row['valid_from'],
            'valid_to' => $row['valid_to'],
            'is_active' => $row['is_active'],
        ]);
    }

    public function batchSize(): int
    {
        return self::BATCH_SIZE;
    }

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'bail',
                'required',
                'string',
                Rule::unique(Coupon::class)
            ],
            'type' => [
                'bail',
                'required',
                'string',
                Rule::enum(DiscountTypeEnum::class)
            ],
            'value' => [
                'bail',
                'required',
                'numeric',
                'min:0'
            ],
            'minimum_order_amount' => [
                'bail',
                'nullable',
                'numeric',
            ],
            'usage_limit' => [
                'bail',
                'nullable',
                'integer',
            ],
            'usage_per_user' => [
                'bail',
                'nullable',
                'integer',
            ],
            'valid_from' => [
                'bail',
                'required',
                'date',
                'after_or_equal:today'
            ],
            'valid_to' => [
                'bail',
                'required',
                'date',
                'after:valid_from'
            ],
            'is_active' => [
                'bail',
                'nullable',
                'boolean'
            ],
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $data['valid_from'] = Date::excelToDateTimeObject($data['valid_from']);
        $data['valid_to'] = Date::excelToDateTimeObject($data['valid_to']);
        return $data;
    }
}
