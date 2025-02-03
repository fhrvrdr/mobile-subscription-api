<?php

namespace App\Services\Application;

use App\DTO\Application\ProviderCredentials;
use App\Exceptions\ApplicationServiceException;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Services\Application\Contracts\ApplicationServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final readonly class ApplicationService implements ApplicationServiceInterface
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository
    ) {}

    public function getCallbackUrl(string $applicationId): string
    {
        return Cache::remember('application_callback_url_'.$applicationId, now()->addWeek(), function () use ($applicationId) {
            return $this->applicationRepository->getCallbackUrl($applicationId);
        });
    }

    public function getProviderCredentials(string $applicationId, string $provider): ProviderCredentials
    {
        $providerCredentials = Cache::get('application_provider_credentials_'.$applicationId);

        if (! is_null($providerCredentials)) {
            $providerCredentials = json_decode($providerCredentials, true);

            if (array_key_exists($provider, $providerCredentials)) {
                return new ProviderCredentials(
                    username: $providerCredentials[$provider]['username'],
                    password: $providerCredentials[$provider]['password']
                );
            }
        }

        $providerCredentials = $this->applicationRepository->getProviderCredentials($applicationId);

        if (! is_null($providerCredentials)) {
            Cache::put('application_provider_credentials_'.$applicationId, json_encode($providerCredentials), now()->addWeek());

            if (array_key_exists($provider, $providerCredentials)) {
                return new ProviderCredentials(
                    username: $providerCredentials[$provider]['username'],
                    password: $providerCredentials[$provider]['password']
                );
            }
        }

        Log::channel('application')->error('Provider credentials not found', [
            'data' => [
                'device' => app('authDevice')->id,
                'application_id' => $applicationId,
                'provider' => $provider,
            ],
        ]);

        throw new ApplicationServiceException('Provider credentials not found');
    }
}
