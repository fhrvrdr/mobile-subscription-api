<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Device;
use App\Repositories\Contracts\DeviceRepositoryInterface;

final readonly class DeviceRepository implements DeviceRepositoryInterface
{
    public function createOrUpdate(array $data): Device
    {
        return Device::updateOrCreate(
            [
                'uid' => $data['uid'],
                'app_id' => $data['app_id'],
            ],
            $data
        );
    }

    public function getDeviceByUidAndAppId(string $uid, string $appId): ?Device
    {
        return Device::search()->where('uid', $uid)->where('app_id', $appId)->first();
    }
}
