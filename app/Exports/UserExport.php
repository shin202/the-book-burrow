<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function query(): Builder
    {
        return User::query()->with('profile');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFill()->setFillType('solid')->getStartColor()->setARGB('bdb9b9');
            }
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'username',
            'email',
            'fullName',
            'gender',
            'age',
            'country',
            'status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->username,
            $row->email,
            $row->profile?->full_name,
            $row->profile?->gender->value,
            now()->diffInYears($row->profile?->date_of_birth),
            $row->profile?->country,
            $row->status->value,
        ];
    }
}
