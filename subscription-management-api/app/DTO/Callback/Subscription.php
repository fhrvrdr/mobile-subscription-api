<?php

declare(strict_types=1);

namespace App\DTO\Callback;

final readonly class Subscription
{
    public function __construct(
        public string $deviceId,
        public string $appId,
        public string $event,
        public string $url,
    ) {}
}
