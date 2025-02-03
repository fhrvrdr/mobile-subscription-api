<?php

declare(strict_types=1);

namespace App\Services\Authentication;

use App\Exceptions\DeviceNotFoundException;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface;
use App\Services\Authentication\Contracts\TokenServiceInterface;

final readonly class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private TokenServiceInterface $tokenService,
    ) {}

    /**
     * @param  array<string, string>  $data
     * @return array<string, string>
     */
    public function registerDevice(array $data): array
    {
        $device = $this->deviceRepository->createOrUpdate($data);

        return $this->tokenService->createToken($device->uid, $device->app_id);
    }

    /**
     * @throws DeviceNotFoundException
     */
    public function verifyDevicePayload(array $data): void
    {
        $device = $this->deviceRepository->getDeviceByUidAndAppId($data['uid'], $data['app_id']);

        if ($device === null) {
            throw new DeviceNotFoundException;
        }

        app()->singleton('authDevice', fn () => $device);
    }
}
