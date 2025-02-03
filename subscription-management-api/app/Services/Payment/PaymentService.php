<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\DTO\Response\PurchaseResponse;
use App\DTO\Response\VerificationResponse;
use App\Exceptions\PaymentServiceException;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Factory\PaymentFactory;

final readonly class PaymentService implements PaymentServiceInterface
{
    /**
     * @throws PaymentServiceException
     */
    public function purchase(string $receipt, string $os): PurchaseResponse
    {
        $paymentGateway = PaymentFactory::create($os);

        return $paymentGateway->pay($receipt);
    }

    /**
     * @throws PaymentServiceException
     */
    public function verifyPayment(string $receipt, string $os): VerificationResponse
    {
        $paymentGateway = PaymentFactory::create($os);

        return $paymentGateway->checkPaymentStatus($receipt);
    }
}
