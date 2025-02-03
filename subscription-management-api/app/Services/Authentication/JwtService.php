<?php

declare(strict_types=1);

namespace App\Services\Authentication;

use App\Services\Authentication\Contracts\TokenServiceInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;

final readonly class JwtService implements TokenServiceInterface
{
    public function createToken(string $uid, string $appId): array
    {
        $payload = $this->generatePayload([
            'uid' => $uid,
            'app_id' => $appId,
        ]);

        $tokenData = $this->generateToken($payload);

        return [
            'expires_at' => Carbon::createFromTimestamp($tokenData['exp'], 'europe/istanbul')->toDateTimeString(),
            'token' => $tokenData['token'],
        ];
    }

    public function decodeToken(string $token): array
    {
        $payload = JWT::decode(
            jwt: $token,
            keyOrKeyArray: new Key(config('auth.jwt.secret'), config('auth.jwt.alg'))
        );

        return (array) $payload->data;
    }

    private function generatePayload(array $data): array
    {
        return [
            'iss' => config('app.name'),
            'iat' => time(),
            'exp' => time() + (int) config('auth.jwt.ttl'),
            'data' => $data,
        ];
    }

    private function generateToken(array $payload): array
    {
        $token = JWT::encode(
            payload: $payload,
            key: config('auth.jwt.secret'),
            alg: config('auth.jwt.alg')
        );

        return [
            'token' => $token,
            'exp' => $payload['exp'],
        ];
    }
}
