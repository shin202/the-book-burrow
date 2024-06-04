<?php

namespace App\Imports\Book;

use App\Models\Author;
use App\Models\Book;
use App\Models\Cost;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BookImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsOnFailure, SkipsEmptyRows, WithValidation
{
    use Importable, SkipsFailures;

    private const int CHUNK_SIZE = 1000;

    private Collection $authors;

    private Collection $genres;

    private Collection $publishers;

    private Collection $bookTitles;

    private Collection $bookIsbns;

    private Collection $bookSlugs;

    private Collection $publisherSlugs;

    public function __construct()
    {
        $this->loadMappings();
    }

    private function loadMappings(): void
    {
        $books = Book::all(['title', 'isbn', 'slug']);
        $publishers = Publisher::all(['id', 'slug']);

        $this->authors = Author::all(['id', 'slug'])->pluck('id', 'slug');
        $this->genres = Genre::all(['id', 'slug'])->pluck('id', 'slug');
        $this->publishers = $publishers->pluck('id', 'slug');
        $this->bookTitles = $books->pluck('title');
        $this->bookIsbns = $books->pluck('isbn');
        $this->bookSlugs = $books->pluck('slug');
        $this->publisherSlugs = $publishers->pluck('slug');
    }

    public function collection(Collection $collection): void
    {
        $collection->lazy()
            ->chunk(self::CHUNK_SIZE)
            ->each(fn(LazyCollection $chunk) => $this->processChunk($chunk));
    }

    private function processChunk(LazyCollection $chunk): void
    {
        $books = $costs = $authors = $genres = [];

        $chunk->each(function ($row) use (&$books, &$costs, &$authors, &$genres) {
            if ($this->isInvalidRow($row)) return;

            $image = Http::get($row['cover_image'])->throw()->body();
            $imagePath = 'uploads/books/' . $row['slug'] . '.jpg';
            $row['cover_image'] = Storage::put($imagePath, $image);
            sleep(1);

            $books[] = $this->transformBookData($row);
            $authors[$row['slug']] = $this->transformAuthorData($row['authors']);
            $genres[$row['slug']] = $this->transformGenreData($row['genres']);
            $costs[$row['slug']] = $row['cost'];
        });

        Book::insert($books);

        $slugs = $chunk->pluck('slug')->toArray();
        $books = Book::whereIn('slug', $slugs)->pluck('id', 'slug');

        $costs = collect($costs)->lazy()->map(function ($cost, $slug) use ($books) {
            return [
                'book_id' => $books[$slug],
                'cost' => $cost,
                'effective_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $authors = collect($authors)->lazy()->map(function ($authorIds, $slug) use ($books) {
            return collect($authorIds)->map(function ($authorId) use ($books, $slug) {
                return [
                    'book_id' => $books[$slug],
                    'author_id' => $authorId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
        })->flatten(1)->toArray();

        $genres = collect($genres)->lazy()->map(function ($genreIds, $slug) use ($books) {
            return collect($genreIds)->map(function ($genreId) use ($books, $slug) {
                return [
                    'book_id' => $books[$slug],
                    'genre_id' => $genreId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
        })->flatten(1)->toArray();

        Cost::insert($costs);
        $this->insertRelatedData($authors, 'author_book');
        $this->insertRelatedData($genres, 'book_genre');
    }

    private function isInvalidRow($row): bool
    {
        return !$this->isUniqueTitle($row['title']) ||
            !$this->isUniqueIsbn($row['isbn']) ||
            !$this->isUniqueSlug($row['slug']) ||
            !$this->isPublisherExists($row['publisher']);
    }

    private function isUniqueTitle(string $title): bool
    {
        return $this->bookTitles->doesntContain($title);
    }

    private function isUniqueIsbn(string $isbn): bool
    {
        return $this->bookIsbns->doesntContain($isbn);
    }

    private function isUniqueSlug(string $slug): bool
    {
        return $this->bookSlugs->doesntContain($slug);
    }

    private function isPublisherExists(string $slug): bool
    {
        return $this->publisherSlugs->contains($slug);
    }

    private function transformBookData($row): array
    {
        return [
            'title' => $row['title'],
            'isbn' => $row['isbn'],
            'description' => $row['description'],
            'number_of_pages' => $row['number_of_pages'],
            'publisher_id' => $this->publishers[$row['publisher']],
            'publication_date' => $row['publication_date'],
            'slug' => $row['slug'],
            'cover_image' => $row['cover_image'],
            'quantity_in_stock' => $row['quantity_in_stock'],
            'price' => $row['price'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function transformAuthorData($authors): array
    {
        $authors = preg_replace('/\s+/', '', $authors);
        $authors = explode(',', $authors);

        $authorIds = [];
        foreach ($authors as $slug) {
            if ($this->authors->keys()->doesntContain($slug)) {
                continue;
            }

            $authorIds[] = $this->authors[$slug];
        }

        return $authorIds;
    }

    private function transformGenreData($genres): array
    {
        $genres = preg_replace('/\s+/', '', $genres);
        $genres = explode(',', $genres);

        $genreIds = [];
        foreach ($genres as $slug) {
            if ($this->genres->keys()->doesntContain($slug)) {
                continue;
            }

            $genreIds[] = $this->genres[$slug];
        }

        return $genreIds;
    }

    private function insertRelatedData(array $data, string $table): void
    {
        DB::table($table)->insert($data);
    }

    public function prepareForValidation($row, $index)
    {
        $row['publication_date'] = Date::excelToDateTimeObject($row['publication_date']);
        $row['isbn'] = (string)$row['isbn'];
        return $row;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'bail',
                'required',
                'string',
                Rule::unique(Book::class)
            ],
            'isbn' => [
                'bail',
                'required',
                'string',
                'min:10',
                'max:13',
                Rule::unique(Book::class)
            ],
            'description' => [
                'bail',
                'nullable',
                'string'
            ],
            'number_of_pages' => [
                'bail',
                'required',
                'integer',
                'min:1'
            ],
            'publisher' => [
                'bail',
                'required',
                'string',
                Rule::in($this->publisherSlugs->toArray())
            ],
            'publication_date' => [
                'bail',
                'required',
                'date',
                'before:today'
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                Rule::unique(Book::class)
            ],
            'authors' => [
                'bail',
                'nullable',
                'string',
            ],
            'genres' => [
                'bail',
                'nullable',
                'string',
            ],
            'cover_image' => [
                'bail',
                'required',
                'string',
                'regex:/^https?:\/\/.*\.(?:png|jpg)$/i'
            ],
            'quantity_in_stock' => [
                'bail',
                'required',
                'integer',
                'min:1'
            ],
            'price' => [
                'bail',
                'required',
                'numeric',
            ],
            'cost' => [
                'bail',
                'required',
                'numeric',
            ],
        ];
    }

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    public function onFailure(Failure ...$failures): void
    {
        Log::info('Failed to import books', ['failures' => $failures]);
    }
}
