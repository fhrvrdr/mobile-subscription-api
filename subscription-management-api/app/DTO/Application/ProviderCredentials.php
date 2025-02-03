<?php

namespace App\DTO\Application;

final readonly class ProviderCredentials
{
    public function __construct(
        public string $username,
        public string $password,
    ) {}
}
