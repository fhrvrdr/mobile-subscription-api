<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
}
