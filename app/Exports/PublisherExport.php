<?php

namespace App\Exports;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class PublisherExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function query(): Builder
    {
        return Publisher::query();
    }

    public function headings(): array
    {
        return [
            '#',
            'name',
            'description',
            'email',
            'phone',
            'website',
            'slug'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->description,
            $row->contact_information['email'],
            $row->contact_information['phone'],
            $row->contact_information['website'],
            $row->slug
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('bdb9b9');
                $event->sheet->getDelegate()->getStyle('C2:C1000')->getAlignment()->setWrapText(true);
            }
        ];
    }
}
