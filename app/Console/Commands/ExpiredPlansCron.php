<?php

namespace App\Console\Commands;

use App\Jobs\SendPlanExpireMailJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpiredPlansCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a cron command to check expired plans and send a message by email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Cron Job running at " . now());

        $expiredUsers = User::where('plan_expires_at', '<', Carbon::now())
            ->where('notified', false)
            ->get();

        foreach ($expiredUsers as $user) {
            SendPlanExpireMailJob::dispatch($user, $user->membershipPlan->name);
        }
    }
}
