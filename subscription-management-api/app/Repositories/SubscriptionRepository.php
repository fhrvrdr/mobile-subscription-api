<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function findByDeviceId(int $deviceID): ?Subscription
    {
        return Subscription::search()->where('device_id', $deviceID)->first();
    }

    public function updateOrCreate(int $deviceId, array $data): Subscription
    {
        $subscription = Subscription::updateOrCreate([
            'device_id' => $deviceId,
        ], $data);

        $subscription->searchable();

        return $subscription;
    }

    /**
     * @throws \Throwable
     */
    public function update(int $subscriptionId, array $data): Subscription
    {
        Subscription::lockForUpdate()->where('id', $subscriptionId)->update($data);
        $subscription = Subscription::find($subscriptionId);
        $subscription->searchable();

        return $subscription;
    }

    public function getExpiredSubscriptions(): Collection
    {
        return Subscription::search('')->options([
            'filter' => sprintf('expires_at < %d AND status = %d',
                now('America/Chicago')->timestamp,
                SubscriptionStatus::ACTIVE->value,
            ),
        ])->get();
    }
}
