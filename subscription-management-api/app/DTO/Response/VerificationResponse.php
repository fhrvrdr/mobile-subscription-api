<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class VerificationResponse
{
    public function __construct(public bool $isExpired) {}
}
