<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum OrderStatusEnum: string
{
    use EnumToArrayTrait;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
