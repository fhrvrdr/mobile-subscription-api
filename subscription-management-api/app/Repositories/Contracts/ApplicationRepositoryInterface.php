<?php

namespace App\Repositories\Contracts;

interface ApplicationRepositoryInterface
{
    public function getCallbackUrl(string $applicationId): ?string;

    public function getProviderCredentials(string $applicationId): ?array;
}
