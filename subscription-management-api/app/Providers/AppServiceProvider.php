<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Application\ApplicationService;
use App\Services\Application\Contracts\ApplicationServiceInterface;
use App\Services\Authentication\AuthenticationService;
use App\Services\Authentication\Contracts\AuthenticationServiceInterface;
use App\Services\Authentication\Contracts\TokenServiceInterface;
use App\Services\Authentication\JwtService;
use App\Services\Callback\CallbackService;
use App\Services\Callback\Contracts\CallbackServiceInterface;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\PaymentService;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
        $this->app->bind(TokenServiceInterface::class, JwtService::class);
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(CallbackServiceInterface::class, CallbackService::class);
        $this->app->singleton(ApplicationServiceInterface::class, ApplicationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
