<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class BookExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function headings(): array
    {
        return [
            'id',
            'title',
            'isbn',
            'description',
            'cover_image',
            'number_of_pages',
            'publisher',
            'publication_date',
            'quantity_in_stock',
            'price',
            'cost',
            'authors',
            'genres',
            'slug',
            'ratings_count',
            'average_rating',
            'orders_count',
            'reviews_count',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->isbn,
            $row->description,
            $row->cover_image,
            $row->number_of_pages,
            $row->publisher?->slug,
            $row->publication_date,
            $row->quantity_in_stock,
            $row->price,
            $row->latestCost->first()?->cost ?? 0,
            $row->authors->pluck('slug')->implode(', '),
            $row->genres->pluck('slug')->implode(', '),
            $row->slug,
            $row->ratings->count(),
            $row->average_rating,
            $row->orders()->sum('quantity'),
            $row->reviews_count,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('0000000000000');
                $event->sheet->getDelegate()->getStyle('J2:J1000')->getNumberFormat()->setFormatCode('$#,##0.00');
                $event->sheet->getDelegate()->getStyle('K2:K1000')->getNumberFormat()->setFormatCode('$#,##0.00');
                $event->sheet->getDelegate()->getStyle('H2:H1000')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            },
        ];
    }

    public function query()
    {
        return Book::query()
            ->with(['publisher', 'authors', 'genres', 'latestCost', 'ratings'])
            ->withCount(['reviews']);
    }
}
