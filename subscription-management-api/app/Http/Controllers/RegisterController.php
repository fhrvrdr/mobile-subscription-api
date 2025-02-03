<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Response\ApiResponse;
use App\DTO\Response\RegisterResponse;
use App\Http\Requests\RegisterRequest;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

final class RegisterController extends Controller
{
    public function __construct(
        readonly private AuthenticationServiceInterface $authenticationService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $tokenData = $this->authenticationService->registerDevice($request->validated());

            return ApiResponse::response(
                httpCode: 200,
                data: new RegisterResponse(token: $tokenData['token'], expires_at: $tokenData['expires_at']),
                message: 'Device registered successfully',
            );
        } catch (\Exception $exception) {
            Log::channel('authentication')->error($exception->getMessage(), [
                'data' => [
                    'device' => $request->validated(),
                    'trace' => $exception->getTraceAsString(),
                ],
            ]);

            return ApiResponse::response(
                httpCode: 500,
                data: [],
                success: false,
                message: 'An error occurred while registering the device',
            );
        }
    }
}
