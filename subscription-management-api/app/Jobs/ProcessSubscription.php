<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\SubscriptionCanceled;
use App\Models\Subscription;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessSubscription implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Subscription $subscription,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        SubscriptionServiceInterface $subscriptionService,
        PaymentServiceInterface $paymentService
    ): void {
        try {
            app()->instance('authDevice', $this->subscription->device);
            $verificationResponse = $paymentService->verifyPayment($this->subscription->receipt, $this->subscription->device->operating_system);

            if ($verificationResponse->isExpired) {
                $updatedSubscription = $subscriptionService->unsubscribe($this->subscription->id);
                SubscriptionCanceled::dispatch($updatedSubscription);
            }
        } catch (\Exception $e) {
            Log::channel('queue')->error('Error while processing subscription: '.$e->getMessage(), [
                'data' => [
                    'device' => $this->subscription->device->id,
                    'subscription' => $this->subscription->toArray(),
                    'trace' => $e->getTraceAsString(),
                ],
            ]);

            $this->fail($e);
        }
    }
}
