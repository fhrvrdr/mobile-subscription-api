<?php

declare(strict_types=1);

namespace App\Services\Authentication\Contracts;

interface TokenServiceInterface
{
    public function createToken(string $uid, string $appId): array;

    public function decodeToken(string $token): array;
}
