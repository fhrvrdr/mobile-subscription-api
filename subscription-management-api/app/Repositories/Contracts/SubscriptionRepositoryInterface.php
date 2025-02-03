<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionRepositoryInterface
{
    public function findByDeviceId(int $deviceID): ?Subscription;

    public function updateOrCreate(int $deviceId, array $data): Subscription;

    public function update(int $subscriptionId, array $data): Subscription;

    public function getExpiredSubscriptions(): Collection;
}
