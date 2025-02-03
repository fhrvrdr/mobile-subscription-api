<?php

declare(strict_types=1);

namespace App\DTO\Response;

use Illuminate\Http\JsonResponse;

final readonly class ApiResponse
{
    /**
     * @param  object|array<string>  $data
     */
    public static function response(
        int $httpCode,
        object|array $data,
        bool $success = true,
        string $message = '',
    ): JsonResponse {
        return response()->json(data: [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], status: $httpCode, options: JSON_UNESCAPED_UNICODE);
    }
}
