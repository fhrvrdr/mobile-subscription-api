<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\DTO\Response\ApiResponse;
use App\Exceptions\DeviceNotFoundException;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface;
use App\Services\Authentication\Contracts\TokenServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthMiddleware
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private AuthenticationServiceInterface $authenticationService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->responseUnauthorized();
        }

        try {
            $payload = $this->tokenService->decodeToken($token);
            $this->authenticationService->verifyDevicePayload($payload);
        } catch (DeviceNotFoundException $e) {
            return $this->responseUnauthorized();
        } catch (\Throwable $th) {
            Log::channel('authentication')->error('Error while decoding token: '.$th->getMessage(), [
                'data' => [
                    'token' => $token,
                    'trace' => $th->getTraceAsString(),
                ],
            ]);

            return $this->responseUnauthorized();
        }

        return $next($request);
    }

    private function responseUnauthorized(): Response
    {
        return ApiResponse::response(
            httpCode: 401,
            data: [],
            success: false, message: 'Unauthorized'
        );
    }
}
