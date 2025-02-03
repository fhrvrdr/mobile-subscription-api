<?php

declare(strict_types=1);

namespace App\Services\Subscription;

use App\DTO\Response\PurchaseResponse;
use App\Enum\SubscriptionStatus;
use App\Events\SubscriptionRenewed;
use App\Events\SubscriptionStarted;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository
    ) {}

    public function checkSubscriptionFromDatabase(int $deviceId): bool
    {
        $subscription = $this->subscriptionRepository->findByDeviceId($deviceId);

        if (! is_null($subscription) && ! $subscription->expires_at->isPast()) {
            return true;
        }

        return false;
    }

    public function subscribe(int $deviceID, string $receipt, PurchaseResponse $purchaseResponse): void
    {
        $subscription = $this->subscriptionRepository->updateOrCreate($deviceID, [
            'expires_at' => $purchaseResponse->expireDate,
            'status' => $purchaseResponse->status,
            'receipt' => $receipt,
        ]);

        if ($subscription->updated_at !== null) {
            SubscriptionRenewed::dispatch($subscription);
        } else {
            SubscriptionStarted::dispatch($subscription);
        }
    }

    public function unsubscribe(int $subscriptionId): Subscription
    {
        return $this->subscriptionRepository->update($subscriptionId, [
            'status' => SubscriptionStatus::INACTIVE->value,
        ]);
    }

    public function getExpiredSubscriptions(): Collection
    {
        return $this->subscriptionRepository->getExpiredSubscriptions();
    }
}
