<?php

Schedule::command('app:check-subscription-status')
    ->everyThirtySeconds()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/check-subscription-status.log'));

Schedule::command('report:generate-subscription')
    ->daily()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/check-subscription-status.log'));
