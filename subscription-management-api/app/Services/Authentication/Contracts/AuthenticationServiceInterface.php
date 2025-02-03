<?php

declare(strict_types=1);

namespace App\Services\Authentication\Contracts;

use App\Exceptions\DeviceNotFoundException;

interface AuthenticationServiceInterface
{
    /**
     * @param  array<string, string>  $data
     */
    public function registerDevice(array $data): array;

    /**
     * @throws DeviceNotFoundException
     */
    public function verifyDevicePayload(array $data): void;
}
