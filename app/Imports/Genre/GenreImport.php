<?php

namespace App\Imports\Genre;

use App\Models\Genre;
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

class GenreImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private const int BATCH_SIZE = 1000;
    private const int CHUNK_SIZE = 1000;

    public function model(array $row): Genre
    {
        return new Genre([
            'name' => $row['name'],
            'description' => $row['description'],
            'slug' => $row['slug'],
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

    public function onFailure(Failure ...$failures)
    {
        // TODO: Implement onFailure() method.
    }


    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                Rule::unique(Genre::class)
            ],
            'description' => [
                'bail',
                'nullable',
                'string',
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Genre::class)
            ],
        ];
    }
}
