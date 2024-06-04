<?php

namespace App\Exports;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class GenreExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFill()->setFillType('solid')->getStartColor()->setARGB('bdb9b9');
                $event->sheet->getDelegate()->getStyle('C2:C1000')->getAlignment()->setWrapText(true);
            }
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'name',
            'description',
            'slug'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->description,
            $row->slug
        ];
    }

    public function query(): Builder
    {
        return Genre::query();
    }
}
