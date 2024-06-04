<?php

namespace App\Imports\Publisher;

use App\Models\Publisher;
use Illuminate\Support\Facades\Log;
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

class PublisherImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    private const int BATCH_SIZE = 1000;
    private const int CHUNK_SIZE = 1000;

    public function model(array $row): Publisher
    {
        $contactInformation = [
            'email' => $row['email'],
            'phone' => $row['phone'],
            'website' => $row['website'],
        ];

        return new Publisher([
            'name' => $row['name'],
            'description' => $row['description'],
            'contact_information' => $contactInformation,
            'slug' => $row['slug'],
        ]);
    }

    public function onFailure(Failure ...$failures): void
    {
        Log::error('Failed to import publishers', ['failures' => $failures]);
    }

    public function batchSize(): int
    {
        return self::BATCH_SIZE;
    }

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    public function prepareForValidation($row)
    {
        $row['phone'] = (string)$row['phone'];
        return $row;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                Rule::unique(Publisher::class)
            ],
            'description' => [
                'bail',
                'required',
                'string',
            ],
            'email' => [
                'bail',
                'required',
                'email',
            ],
            'phone' => [
                'bail',
                'required',
                'string',
                'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/'
            ],
            'website' => [
                'bail',
                'required',
                'url',
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Publisher::class)
            ]
        ];
    }
}
