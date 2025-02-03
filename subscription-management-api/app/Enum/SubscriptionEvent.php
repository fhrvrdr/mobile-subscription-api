<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionEvent: string
{
    case STARTED = 'Subscription started';
    case RENEWED = 'Subscription renewed';
    case CANCELLED = 'Subscription cancelled';
}
