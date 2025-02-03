<?php

declare(strict_types=1);

namespace App\Listeners;

use App\DTO\Callback\Subscription as SubscriptionDTO;
use App\Events\SubscriptionCanceled;
use App\Events\SubscriptionRenewed;
use App\Events\SubscriptionStarted;
use App\Facade\ApplicationSettings;
use App\Services\Callback\Contracts\CallbackServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

final class SubscriptionListener implements ShouldQueue
{
    public ?string $connection = 'redis';

    public ?string $queue = 'listeners';

    public int $delay = 10;

    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly CallbackServiceInterface $callbackService,
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionStarted|SubscriptionRenewed|SubscriptionCanceled $event): void
    {
        $subscriptionModel = $event->subscription;
        $appId = $subscriptionModel->device->app_id;
        $callbackUrl = ApplicationSettings::getCallbackUrl($appId);

        $subscription = new SubscriptionDTO(
            deviceId: $subscriptionModel->device->uid,
            appId: $appId,
            event: $event::class,
            url: $callbackUrl,
        );

        $this->callbackService->sendNotification($subscription);
    }

    /**
     * Handle a job failure.
     */
    public function failed(SubscriptionStarted|SubscriptionRenewed|SubscriptionCanceled $event, \Throwable $exception): void
    {
        Log::channel('subscription')->error('SubscriptionListener failed', [
            'data' => [
                'device' => $event->subscription->device->id,
                'event' => $event::class,
                'model' => $event->subscription->toArray(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }
}
