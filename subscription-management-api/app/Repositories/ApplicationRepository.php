<?php

namespace App\Repositories;

use App\Models\Application;
use App\Repositories\Contracts\ApplicationRepositoryInterface;

final readonly class ApplicationRepository implements ApplicationRepositoryInterface
{
    public function getCallbackUrl(string $applicationId): ?string
    {
        return Application::where('app_id', $applicationId)->value('callback_url');
    }

    public function getProviderCredentials(string $applicationId): ?array
    {
        return Application::where('app_id', $applicationId)->value('provider_credentials');
    }
}
