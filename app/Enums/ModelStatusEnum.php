<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum ModelStatusEnum: string
{
    use EnumToArrayTrait;

    case AVAILABLE = 'available';
    case DELETED = 'deleted';
}
