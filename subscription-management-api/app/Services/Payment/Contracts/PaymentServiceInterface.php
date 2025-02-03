<?php

declare(strict_types=1);

namespace App\Services\Payment\Contracts;

use App\DTO\Response\PurchaseResponse;
use App\DTO\Response\VerificationResponse;

interface PaymentServiceInterface
{
    public function purchase(string $receipt, string $os): PurchaseResponse;

    public function verifyPayment(string $receipt, string $os): VerificationResponse;
}
