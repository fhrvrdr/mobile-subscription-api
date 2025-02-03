<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Device;

interface DeviceRepositoryInterface
{
    public function createOrUpdate(array $data): Device;

    public function getDeviceByUidAndAppId(string $uid, string $appId): ?Device;
}
