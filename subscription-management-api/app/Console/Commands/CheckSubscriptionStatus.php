<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessSubscription;
use App\Models\Subscription;
use App\Services\Subscription\Contracts\SubscriptionServiceInterface;
use Illuminate\Console\Command;

final class CheckSubscriptionStatus extends Command
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check the expiration date of the subscription. If the subscription is expired, it will update the subscription status.';

    public function handle(): void
    {
        $subscriptions = $this->subscriptionService->getExpiredSubscriptions();

        if ($subscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');

            return;
        }

        $subscriptions->chunk(1000)->each(function ($chunk) {
            $chunk->each(function (Subscription $subscription) {
                ProcessSubscription::dispatch($subscription)->onQueue('subscriptions');
            });
        });

        $this->info('Expired subscriptions have been processed.');
    }
}
