<?php

namespace App\Exports;

use App\Enums\DiscountTypeEnum;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use NumberFormatter;

class CouponExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function query(): Builder
    {
        return Coupon::query();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:J1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:J1')->getFill()->setFillType('solid')->getStartColor()->setARGB('bdb9b9');
            }
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'code',
            'type',
            'value',
            'minimum_order_amount',
            'usage_limit',
            'usage_per_user',
            'valid_from',
            'valid_to',
            'is_active',
        ];
    }

    public function map($row): array
    {
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $value = $row->type === DiscountTypeEnum::PERCENTAGE ? $row->value . '%' : $fmt->formatCurrency($row->value, 'USD');

        return [
            $row->id,
            $row->code,
            $row->type->value,
            $value,
            $fmt->formatCurrency($row->minimum_order_amount, 'USD'),
            $row->usage_limit,
            $row->usage_per_user,
            $row->valid_from->format('Y-m-d h:i:s'),
            $row->valid_to->format('Y-m-d h:i:s'),
            $row->is_active,
        ];
    }
}
