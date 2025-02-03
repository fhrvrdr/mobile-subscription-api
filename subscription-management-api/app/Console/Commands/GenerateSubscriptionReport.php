<?php

namespace App\Console\Commands;

use App\Enum\SubscriptionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateSubscriptionReport extends Command
{
    protected $signature = 'report:generate-subscription';

    protected $description = 'Generates subscription reports based on day, operating system';

    public function handle()
    {
        $date = Carbon::today('America/Chicago')->toDateString();

        $startedSubscriptions = DB::table('subscriptions')
            ->whereDate('created_at', '=',$date)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->count();

        $endedSubscriptions = DB::table('subscriptions')
            ->whereDate('expires_at', '=', $date)
            ->where('status', SubscriptionStatus::INACTIVE)
            ->count();

        $renewedSubscriptions = DB::table('subscriptions')
            ->whereDate('updated_at', '=', $date)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->count();

        $this->info("Started subscriptions: $startedSubscriptions");
        $this->info("Ended subscriptions: $endedSubscriptions");
        $this->info("Renewed subscriptions: $renewedSubscriptions");
    }
}
