<?php

namespace App\Services\Callback\Contracts;

use App\DTO\Callback\Subscription;

interface CallbackServiceInterface
{
    public function sendNotification(Subscription $subscription): void;
}
