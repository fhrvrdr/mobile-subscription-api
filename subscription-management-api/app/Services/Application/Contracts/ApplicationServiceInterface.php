<?php

namespace App\Services\Application\Contracts;

use App\DTO\Application\ProviderCredentials;

interface ApplicationServiceInterface
{
    public function getCallbackUrl(string $applicationId): string;

    public function getProviderCredentials(string $applicationId, string $provider): ProviderCredentials;
}
