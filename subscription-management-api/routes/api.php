<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'register');
});

Route::middleware('jwt-auth')->group(function () {
    Route::controller(PaymentController::class)->prefix('/payment')->group(function () {
        Route::post('/purchase', 'purchase');
    });

    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/check-subscription', 'checkSubscription');
    });
});
