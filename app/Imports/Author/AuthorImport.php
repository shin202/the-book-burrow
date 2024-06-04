<?php

namespace App\Imports\Author;

use App\Models\Author;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

class AuthorImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    private const int BATCH_SIZE = 1000;

    private const int CHUNK_SIZE = 1000;

    public function model(array $row): Author
    {
        $avatar = null;
        if (isset($row['avatar'])) {
            $avatarContents = Http::get($row['avatar'])->body();
            $avatar = 'uploads/authors/' . $row['slug'] . '/' . Str::random(40) . '.jpg';
            Storage::put($avatar, $avatarContents);
        }

        return new Author([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'biography' => $row['biography'],
            'avatar' => $avatar,
            'slug' => $row['slug'],
        ]);
    }

    public function onFailure(Failure ...$failures)
    {
        Log::error('Failed to import authors', ['failures' => $failures]);
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
            'first_name' => [
                'bail',
                'required',
                'string',
            ],
            'last_name' => [
                'bail',
                'nullable',
                'string',
            ],
            'biography' => [
                'bail',
                'nullable',
                'string',
            ],
            'avatar' => [
                'bail',
                'nullable',
                'regex:/^https?:\/\/.*\.(?:png|jpg)$/i'
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Author::class)
            ]
        ];
    }
}
