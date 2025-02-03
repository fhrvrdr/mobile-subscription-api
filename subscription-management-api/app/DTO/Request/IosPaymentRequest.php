<?php

declare(strict_types=1);

namespace App\DTO\Request;

final readonly class IosPaymentRequest
{
    public function __construct(public string $receipt, public string $url) {}

    public function forRequest(): array
    {
        return [
            'receipt' => $this->receipt,
        ];
    }
}
