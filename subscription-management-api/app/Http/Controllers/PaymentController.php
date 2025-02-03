<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Response\ApiResponse;
use App\Http\Requests\PurchaseRequest;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use Illuminate\Support\Facades\Log;

final class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService,
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {}

    public function purchase(PurchaseRequest $request)
    {
        try {
            $device = app('authDevice');

            $paymentResponse = $this->paymentService->purchase($request->receipt, $device->operating_system);
            $this->subscriptionService->subscribe($device->id, $request->receipt, $paymentResponse);

            return ApiResponse::response(
                httpCode: 200,
                data: $paymentResponse,
            );
        } catch (\Exception $exception) {
            Log::channel('payment')->error($exception->getMessage(), [
                'data' => [
                    'device' => app('authDevice'),
                    'receipt' => $request->receipt,
                    'trace' => $exception->getTraceAsString(),
                ],
            ]);

            return ApiResponse::response(
                httpCode: 500,
                data: [],
                success: false,
                message: 'An error occurred while processing your request.',
            );
        }
    }
}
