<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Response\ApiResponse;
use App\DTO\Response\SubscriptionResponse;
use App\Models\Device;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

final class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionServiceInterface $subscriptionService) {}

    public function checkSubscription(): JsonResponse
    {
        try {
            /** @var Device $device */
            $device = app('authDevice');

            $subscriptionStatus = $this->subscriptionService->checkSubscriptionFromDatabase($device->id);
            $subscriptionStatusMessage = $subscriptionStatus ? 'Subscription is active' : 'Subscription is inactive';

            return ApiResponse::response(
                httpCode: 200,
                data: new SubscriptionResponse($subscriptionStatusMessage)
            );
        } catch (\Exception $e) {
            Log::channel('subscription')->error($e->getMessage(), [
                'data' => ['device' => $device->id, 'trace' => $e->getTraceAsString()],
            ]);

            return ApiResponse::response(
                httpCode: 500,
                data: [],
                success: false,
                message: 'An error occurred while checking the subscription',
            );
        }
    }
}
