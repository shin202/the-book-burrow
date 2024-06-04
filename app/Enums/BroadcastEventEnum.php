<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum BroadcastEventEnum: string
{
    use EnumToArrayTrait;

    case EXPORTER_PROCESSING = 'exporter.processing';

    case IMPORTER_PROCESSING = 'importer.processing';

    case ORDER_PLACED = 'order.placed';
}
