<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum DiscountTypeEnum: string
{
    use EnumToArrayTrait;

    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
