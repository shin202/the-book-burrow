<?php

namespace App\Traits;

trait TimeUnitToSqlFormat
{
    public function toSqlFormat(string $unit = 'days'): string
    {
        $formats = [
            'days' => '%m-%d',
            'weeks' => '%v-%Y',
            'months' => '%m-%Y',
            'years' => '%Y',
        ];

        return $formats[$unit];
    }
}
