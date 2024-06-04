<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum StockStatusEnum: string
{
    use EnumToArrayTrait;

    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
    case LOW_STOCK = 'low_stock';
}
