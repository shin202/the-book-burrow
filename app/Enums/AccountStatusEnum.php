<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum AccountStatusEnum: string
{
    use EnumToArrayTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
