<?php

declare(strict_types=1);

namespace App\Services\Subscription\Contracts;

use App\DTO\Response\PurchaseResponse;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionServiceInterface
{
    public function checkSubscriptionFromDatabase(int $deviceId): bool;

    public function subscribe(int $deviceID, string $receipt, PurchaseResponse $purchaseResponse): void;

    public function unsubscribe(int $subscriptionId): Subscription;

    public function getExpiredSubscriptions(): Collection;
}
