<?php

namespace App\Exports;

use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class AuthorExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function query(): Builder
    {
        return Author::query();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFill()->setFillType('solid')->getStartColor()->setARGB('bdb9b9');
                $event->sheet->getDelegate()->getStyle('D2:D1000')->getAlignment()->setWrapText(true);
            }
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'biography',
            'avatar',
            'slug'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->first_name,
            $row->last_name,
            $row->biography,
            $row->avatar ? Config::get('app.url') . '/storage/' . $row->avatar : null,
            $row->slug
        ];
    }
}
