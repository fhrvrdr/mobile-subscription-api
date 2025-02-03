<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class RegisterResponse
{
    public function __construct(
        public string $token,
        public string $expires_at,
    ) {}
}
