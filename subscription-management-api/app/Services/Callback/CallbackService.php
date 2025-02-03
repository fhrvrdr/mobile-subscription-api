<?php

declare(strict_types=1);

namespace App\Services\Callback;

use App\DTO\Callback\Subscription as SubscriptionDTO;
use App\Exceptions\CallbackServiceException;
use App\Services\Callback\Contracts\CallbackServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final readonly class CallbackService implements CallbackServiceInterface
{
    public function sendNotification(SubscriptionDTO $subscription): void
    {
        $response = Http::post($subscription->url, [
            'deviceId' => $subscription->deviceId,
            'appId' => $subscription->appId,
            'event' => $subscription->event,
        ]);

        if ($response->getStatusCode() !== 200) {
            Log::channel('callback')->error('Callback Notification failed', [
                'data' => [
                    'device' => $subscription->deviceId,
                    'appId' => $subscription->appId,
                    'event' => $subscription->event,
                    'url' => $subscription->url,
                    'response' => $response->json(),
                ],
            ]);

            throw new CallbackServiceException('Callback Notification failed');
        }

        Log::channel('callback')->info('Callback Notification sent', [
            'data' => [
                'device' => $subscription->deviceId,
                'appId' => $subscription->appId,
                'event' => $subscription->event,
                'url' => $subscription->url,
            ],
        ]);
    }
}
