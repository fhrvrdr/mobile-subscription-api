<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class PurchaseResponse
{
    public function __construct(
        public bool $status,
        public string $expireDate
    ) {}
}
