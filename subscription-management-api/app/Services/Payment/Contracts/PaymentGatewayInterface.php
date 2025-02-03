<?php

declare(strict_types=1);

namespace App\Services\Payment\Contracts;

use App\DTO\Response\PurchaseResponse;
use App\DTO\Response\VerificationResponse;

interface PaymentGatewayInterface
{
    public function pay(string $receipt): PurchaseResponse;

    public function checkPaymentStatus(string $receipt): VerificationResponse;
}
