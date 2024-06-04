<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum CouponStatusEnum: string
{
    use EnumToArrayTrait;

    case ENABLED = 'enabled';

    case DISABLED = 'disabled';

    case EXPIRED = 'expired';

    case LIMIT_REACHED = 'limit_reached';

    case AVAILABLE = 'available';
}
