<?php

declare(strict_types=1);

namespace App\Services\Payment\Factory;

use App\Exceptions\PaymentServiceException;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

final readonly class PaymentFactory
{
    /**
     * @throws PaymentServiceException
     */
    public static function create(string $paymentGateway): PaymentGatewayInterface
    {
        return match ($paymentGateway) {
            'ios' => app(IosPayment::class),
            'android' => app(AndroidPayment::class),
            default => throw new PaymentServiceException('Invalid payment gateway'),
        };
    }
}
