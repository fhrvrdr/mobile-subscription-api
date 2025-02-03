<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class SubscriptionResponse
{
    public function __construct(
        public string $subscriptionStatus
    ) {}
}
